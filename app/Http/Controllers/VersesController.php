<?php

namespace App\Http\Controllers;

use App\Models\verses;
use App\Models\Word;
use Illuminate\Http\Request;

class VersesController extends Controller
{
    /**
     * Helper: ambil semua Word untuk daftar verse_key, group per verse_key
     */
    protected function fetchWordsForKeys(array $keys)
    {
        // build where clause: location LIKE 'key:%' OR ...
        $q = Word::query();
        $q->where(function ($q2) use ($keys) {
            foreach ($keys as $key) {
                $q2->orWhere('location', 'like', $key . ':%');
            }
        });
        // urut berdasarkan position
        $all = $q->orderBy('position')->get();

        // groupBy prefix "surah:ayah"
        return $all->groupBy(function ($w) {
            [$s, $v, $p] = explode(':', $w->location);
            return "$s:$v";
        });
    }

    /**
     * GET /verses?words=
     */
    public function getAllVerses(Request $request)
    {
        $withWords = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        // 1) ambil semua verses
        $verses = Verses::select([
            'id',
            'verse_number',
            'verse_key',
            'hizb_number',
            'rub_el_hizb_number',
            'ruku_number',
            'manzil_number',
            'sajdah_number',
            'text_uthmani',
            'page_number',
            'juz_number'
        ])->get();

        if ($withWords) {
            $keys    = $verses->pluck('verse_key')->all();
            $grouped = $this->fetchWordsForKeys($keys);

            // attach words ke setiap verse
            foreach ($verses as $v) {
                $v->words = $grouped->get($v->verse_key, collect());
            }
        }

        return response()->json(['verses' => $verses]);
    }

    /**
     * GET /verses/page/{noPage}?per_page=&page=&words=
     */
    public function by_page($noPage, Request $request)
    {
        if ($noPage < 1 || $noPage > 604) {
            return response()->json(['message' => 'Halaman hanya dari 1 sampai 604'], 400);
        }

        $perPage     = (int)$request->query('per_page', 10);
        $currentPage = (int)$request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        // 1) ambil verse slice
        $base = Verses::where('page_number', $noPage)
            ->select([
                'id',
                'verse_number',
                'verse_key',
                'hizb_number',
                'rub_el_hizb_number',
                'ruku_number',
                'manzil_number',
                'sajdah_number',
                'text_uthmani',
                'page_number',
                'juz_number'
            ]);

        $total  = $base->count();
        $verses = $base->skip($offset)->take($perPage)->get();

        if ($withWords && $verses->isNotEmpty()) {
            $keys    = $verses->pluck('verse_key')->all();
            $grouped = $this->fetchWordsForKeys($keys);
            foreach ($verses as $v) {
                $v->words = $grouped->get($v->verse_key, collect());
            }
        }

        return response()->json([
            'verses'     => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $currentPage * $perPage < $total ? $currentPage + 1 : null,
                'total_pages'   => (int)ceil($total / $perPage),
                'total_records' => $total,
            ],
        ]);
    }

    /**
     * GET /verses/verse/{surah}:{ayah}?words=
     */
    public function by_verse($verseKey, Request $request)
    {
        if (! preg_match('/^\d{1,3}:\d{1,3}$/', $verseKey)) {
            return response()->json(['message' => 'Format verse adalah no_surah:no_ayah'], 400);
        }

        $withWords = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $verse = Verses::where('verse_key', $verseKey)
            ->select([
                'id',
                'verse_number',
                'verse_key',
                'hizb_number',
                'rub_el_hizb_number',
                'ruku_number',
                'manzil_number',
                'sajdah_number',
                'text_uthmani',
                'page_number',
                'juz_number'
            ])->first();

        if (! $verse) {
            return response()->json(['message' => 'Verse tidak ditemukan'], 404);
        }

        if ($withWords) {
            $grouped      = $this->fetchWordsForKeys([$verseKey]);
            $verse->words = $grouped->get($verseKey, collect());
        }

        return response()->json(['verse' => $verse]);
    }

    /**
     * GET /verses/chapter/{chapter}?per_page=&page=&words=
     */
    public function byChapter($chapter, Request $request)
    {
        if ($chapter < 1 || $chapter > 114) {
            return response()->json(['message' => 'Chapter hanya dari 1 sampai 114'], 400);
        }

        $perPage     = (int)$request->query('per_page', 286);
        $currentPage = (int)$request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $base = Verses::where('verse_key', 'LIKE', $chapter . ':%')
            ->select([
                'id',
                'verse_number',
                'verse_key',
                'hizb_number',
                'rub_el_hizb_number',
                'ruku_number',
                'manzil_number',
                'sajdah_number',
                'text_uthmani',
                'page_number',
                'juz_number'
            ]);

        $total  = $base->count();
        $verses = $base->skip($offset)->take($perPage)->get();

        if ($withWords && $verses->isNotEmpty()) {
            $keys    = $verses->pluck('verse_key')->all();
            $grouped = $this->fetchWordsForKeys($keys);
            foreach ($verses as $v) {
                $v->words = $grouped->get($v->verse_key, collect());
            }
        }

        return response()->json([
            'verses'     => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $currentPage * $perPage < $total ? $currentPage + 1 : null,
                'total_pages'   => (int)ceil($total / $perPage),
                'total_records' => $total,
            ],
        ]);
    }

    /**
     * GET /verses/juz/{juzNumber}?per_page=&page=&words=
     */
    public function byJuz($juzNumber, Request $request)
    {
        if ($juzNumber < 1 || $juzNumber > 30) {
            return response()->json(['message' => 'Juz hanya dari 1 sampai 30'], 400);
        }

        $perPage     = (int)$request->query('per_page', 20);
        $currentPage = (int)$request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $base = Verses::where('juz_number', $juzNumber)
            ->select([
                'id',
                'verse_number',
                'verse_key',
                'hizb_number',
                'rub_el_hizb_number',
                'ruku_number',
                'manzil_number',
                'sajdah_number',
                'text_uthmani',
                'page_number',
                'juz_number'
            ]);

        $total  = $base->count();
        $verses = $base->skip($offset)->take($perPage)->get();

        if ($withWords && $verses->isNotEmpty()) {
            $keys    = $verses->pluck('verse_key')->all();
            $grouped = $this->fetchWordsForKeys($keys);
            foreach ($verses as $v) {
                $v->words = $grouped->get($v->verse_key, collect());
            }
        }

        return response()->json([
            'verses'     => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $currentPage * $perPage < $total ? $currentPage + 1 : null,
                'total_pages'   => (int)ceil($total / $perPage),
                'total_records' => $total,
            ],
        ]);
    }
}
