<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;

class VersesController extends Controller
{
    // helper untuk grouping Word collection jadi array verse-level
    protected function groupWordsByVerse($words)
    {
        // $words: Illuminate\Support\Collection of Word
        $grouped = $words
            ->sortBy('location')
            ->groupBy(function ($w) {
                // split "1:2:3" → ["1","2","3"], ambil "1:2"
                [$s, $v, /*$p*/] = explode(':', $w->location);
                return "$s:$v";
            });

        // map ke format API: [
        //   ['verse_key'=>'1:2','verse_number'=>2,'words'=>[...]],
        //   …
        // ]
        return $grouped->map(function ($wordsInVerse, $verseKey) {
            [, $ayah] = explode(':', $verseKey);
            return [
                'verse_key'     => $verseKey,
                'verse_number'  => (int)$ayah,
                'words'         => $wordsInVerse->values(),
            ];
        })->values();
    }

    // GET /verses
    public function getAllVerses(Request $request)
    {
        $allWords = Word::all();
        $verses   = $this->groupWordsByVerse($allWords);

        return response()->json(['verses' => $verses]);
    }

    // GET /verses/page/{noPage}?per_page=…&page=…
    public function by_page($noPage, Request $request)
    {
        if ($noPage < 1 || $noPage > 604) {
            return response()->json(['message' => 'Halaman hanya dari 1 sampai 604'], 400);
        }

        $perPage     = (int) $request->query('per_page', 10);
        $currentPage = (int) $request->query('page', 1);

        // ambil semua words di halaman itu
        $wordsOnPage = Word::where('page_number', $noPage)->get();

        // grouping per verse lalu paginate
        $verses = $this->groupWordsByVerse($wordsOnPage);
        $total  = $verses->count();

        $sliced = $verses
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $totalPages = (int) ceil($total / $perPage);
        $nextPage   = $currentPage < $totalPages ? $currentPage + 1 : null;

        return response()->json([
            'verses'     => $sliced,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $nextPage,
                'total_pages'   => $totalPages,
                'total_records' => $total,
            ],
        ]);
    }

    // GET /verses/verse/{verseKey}
    public function by_verse($verseKey)
    {
        // validasi format "surah:ayah"
        if (! preg_match('/^\d{1,3}:\d{1,3}$/', $verseKey)) {
            return response()->json(['message' => 'Format verse adalah no_surah:no_ayah'], 400);
        }

        // ambil semua words yang location-nya "1:2:%"
        $words = Word::where('location', 'like', $verseKey . ':%')
            ->orderBy('position')
            ->get();

        if ($words->isEmpty()) {
            return response()->json(['message' => 'Verse tidak ditemukan'], 404);
        }

        return response()->json([
            'verse' => [
                'verse_key'    => $verseKey,
                'verse_number' => (int) explode(':', $verseKey)[1],
                'words'        => $words,
            ]
        ]);
    }

    // GET /verses/chapter/{chapter}?per_page=…&page=…
    public function byChapter($chapter, Request $request)
    {
        if ($chapter < 1 || $chapter > 114) {
            return response()->json(['message' => 'Chapter hanya dari 1 sampai 114'], 400);
        }

        $perPage     = (int) $request->query('per_page', 286);
        $currentPage = (int) $request->query('page', 1);

        // ambil semua words di chapter itu
        $words = Word::where('location', 'like', $chapter . ':%')->get();

        // grouping per verse lalu paginate
        $verses      = $this->groupWordsByVerse($words);
        $total       = $verses->count();
        $sliced      = $verses
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();
        $totalPages  = (int) ceil($total / $perPage);
        $nextPage    = $currentPage < $totalPages ? $currentPage + 1 : null;

        return response()->json([
            'verses'     => $sliced,
            'pagination' => [
                'per_page'      => $perPage,
                'current_page'  => $currentPage,
                'next_page'     => $nextPage,
                'total_pages'   => $totalPages,
                'total_records' => $total,
            ],
        ]);
    }
}
