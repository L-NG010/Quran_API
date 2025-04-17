<?php

namespace App\Http\Controllers;

use App\Models\Verses;
use Illuminate\Http\Request;

class VersesController extends Controller
{
    /**
     * GET /verses
     * Jika ?words=true maka nested words (tanpa audio_url) akan disertakan.
     */
    public function getAllVerses(Request $request)
    {
        $withWords = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $query = Verses::select([
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
            'juz_number',
        ]);

        if ($withWords) {
            $query->with(['words' => function($q) {
                $q->select([
                    'original_id as id',
                    'verse_id',
                    'position',
                    'char_type_name',
                    'location',
                    'text_uthmani',
                    'page_number',
                    'line_number',
                    'text',
                    'translation_text as translation_text',
                    'translation_language_name as translation_language_name',
                    'transliteration_text as transliteration_text',
                    'transliteration_language_name as transliteration_language_name',
                ]);
            }]);
        }

        $verses = $query->get();

        return response()->json([
            'verses' => $verses,
        ]);
    }

    /**
     * GET /verses/page/{noPage}?per_page=&page=&words=
     */
    public function by_page($noPage, Request $request)
    {
        if ($noPage < 1 || $noPage > 604) {
            return response()->json(['message' => 'Halaman hanya dari 1 sampai 604'], 400);
        }

        $perPage     = (int) $request->query('per_page', 10);
        $currentPage = (int) $request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $query = Verses::where('page_number', $noPage)
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
                'juz_number',
            ]);

        if ($withWords) {
            $query->with(['words' => function($q) {
                $q->select([
                    'original_id as id',
                    'verse_id',
                    'position',
                    'char_type_name',
                    'location',
                    'text_uthmani',
                    'page_number',
                    'line_number',
                    'text',
                    'translation_text as translation_text',
                    'translation_language_name as translation_language_name',
                    'transliteration_text as transliteration_text',
                    'transliteration_language_name as transliteration_language_name',
                ]);
            }]);
        }

        $totalRecords = $query->count();
        $verses       = $query
            ->skip($offset)
            ->take($perPage)
            ->get();

        $totalPages = (int) ceil($totalRecords / $perPage);
        $nextPage   = $currentPage < $totalPages ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $nextPage,
                'total_pages'   => $totalPages,
                'total_records' => $totalRecords,
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

        [$surah, $ayah] = explode(':', $verseKey);
        $withWords      = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $query = Verses::where('verse_key', $verseKey)
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
                'juz_number',
            ]);

        if ($withWords) {
            $query->with(['words' => function($q) {
                $q->select([
                    'original_id as id',
                    'verse_id',
                    'position',
                    'char_type_name',
                    'location',
                    'text_uthmani',
                    'page_number',
                    'line_number',
                    'text',
                    'translation_text as translation_text',
                    'translation_language_name as translation_language_name',
                    'transliteration_text as transliteration_text',
                    'transliteration_language_name as transliteration_language_name',
                ]);
            }]);
        }

        $verse = $query->first();
        if (! $verse) {
            return response()->json(['message' => 'Verse tidak ditemukan'], 404);
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

        $perPage     = (int) $request->query('per_page', 286);
        $currentPage = (int) $request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $query = Verses::where('verse_key', 'LIKE', $chapter . ':%')
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
                'juz_number',
            ]);

        if ($withWords) {
            $query->with(['words' => function($q) {
                $q->select([
                    'original_id as id',
                    'verse_id',
                    'position',
                    'char_type_name',
                    'location',
                    'text_uthmani',
                    'page_number',
                    'line_number',
                    'text',
                    'translation_text as translation_text',
                    'translation_language_name as translation_language_name',
                    'transliteration_text as transliteration_text',
                    'transliteration_language_name as transliteration_language_name',
                ]);
            }]);
        }

        $totalRecords = $query->count();
        $verses       = $query
            ->skip($offset)
            ->take($perPage)
            ->get();

        $totalPages = (int) ceil($totalRecords / $perPage);
        $nextPage   = $currentPage < $totalPages ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $nextPage,
                'total_pages'   => $totalPages,
                'total_records' => $totalRecords,
            ],
        ]);
    }

    /**
     * GET /verses/juz/{juz}?per_page=&page=&words=
     */
    public function byJuz($juz, Request $request)
    {
        if ($juz < 1 || $juz > 30) {
            return response()->json(['message' => 'Juz hanya dari 1 sampai 30'], 400);
        }

        $perPage     = (int) $request->query('per_page', 10);
        $currentPage = (int) $request->query('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $withWords   = filter_var($request->query('words', false), FILTER_VALIDATE_BOOLEAN);

        $query = Verses::where('juz_number', $juz)
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
                'juz_number',
            ]);

        if ($withWords) {
            $query->with(['words' => function($q) {
                $q->select([
                    'original_id as id',
                    'verse_id',
                    'position',
                    'char_type_name',
                    'location',
                    'text_uthmani',
                    'page_number',
                    'line_number',
                    'text',
                    'translation_text as translation_text',
                    'translation_language_name as translation_language_name',
                    'transliteration_text as transliteration_text',
                    'transliteration_language_name as transliteration_language_name',
                ]);
            }]);
        }

        $totalRecords = $query->count();
        $verses       = $query
            ->skip($offset)
            ->take($perPage)
            ->get();

        $totalPages = (int) ceil($totalRecords / $totalRecords);
        $nextPage   = $currentPage < $totalPages ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $nextPage,
                'total_pages'   => $totalPages,
                'total_records' => $totalRecords,
            ],
        ]);
    }
}
