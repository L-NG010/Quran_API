<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Verses;
use App\Models\Word;
use App\Models\ScriptText;
use App\Models\SearchAlias;
use Illuminate\Http\Request;

class SearchingController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q'));

        // 1. Cek tabel search_aliases
        $alias = SearchAlias::where('keyword', $query)->first();
        if ($alias) {
            return $this->handleAlias($alias, $query);
        }

        // 2. Deteksi perintah khusus: "halaman {n}"
        if (preg_match('/^halaman\s*(\d+)$/i', $query, $m) || preg_match('/^page\s*(\d+)$/i', $query, $m)) {
            return $this->searchHalamanWords($m[1], $query);
        }
        // 3. Deteksi perintah khusus: "juz {n}"
        if (preg_match('/^juz\s*(\d+)$/i', $query, $m)) {
            return $this->searchJuzWords($m[1], $query);
        }
        // 4. Deteksi format surah:ayat, misal "2:255"
        if (preg_match('/^\d+:\d+$/', $query)) {
            return $this->searchSpecificVerse($query, $query);
        }

        // 5. Fallback: cari di tabel chapters
        $chapters = Chapter::whereRaw('MATCH(name_simple) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
            ->orWhereRaw('MATCH(name_complex) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
            ->orWhereRaw('MATCH(nama_alt) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
            ->orWhereRaw('JSON_SEARCH(translated_name, "one", ?) IS NOT NULL', [$query])
            ->get();

        if ($chapters->isEmpty()) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Not found'
            ], 404);
        }

        return response()->json([
            'search_query' => $query,
            'results'      => $chapters->map(fn($ch) => $this->formatSurat($ch)),
        ]);
    }

    protected function handleAlias(SearchAlias $alias, string $query)
    {
        switch ($alias->type) {
            case 'ayat':
                return $this->searchAyat($alias->reference_id, $query);
            case 'halaman':
                return $this->searchHalamanWords($alias->reference_id, $query);
            case 'juz':
                return $this->searchJuzWords($alias->reference_id, $query);
            case 'surat':
                return $this->searchSuratById($alias->reference_id, $query);
            default:
                return response()->json([
                    'search_query' => $query,
                    'message'      => "Unsupported alias type: {$alias->type}"
                ], 400);
        }
    }

    protected function searchAyat(string $verseKey, string $query)
    {
        $verse = Verses::where('verse_key', $verseKey)->first();
        if (! $verse) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Verse not found'
            ], 404);
        }

        $text = ScriptText::where('verse_key', $verseKey)->value('text_imlaei');
        return response()->json([
            'search_query' => $query,
            'verse_key'    => $verse->verse_key,
            'script_text'  => $text
        ]);
    }

    protected function searchHalamanWords(string $page, string $query)
    {
        $verses = Verses::where('page_number', $page)->get();
        if ($verses->isEmpty()) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Halaman hanya terdiri dari 1 hingga 604'
            ], 404);
        }

        // group by chapter dari verse_key
        $versesByChapter = $verses->groupBy(fn(Verses $v) => explode(':', $v->verse_key)[0]);

        $results = [];
        foreach ($versesByChapter as $chapterId => $chapterVerses) {
            $chapter = Chapter::find((int)$chapterId);
            if (! $chapter) continue;

            $keys = $chapterVerses->pluck('verse_key')->toArray();
            $words = Word::where(function($q) use ($keys) {
                    foreach ($keys as $k) {
                        $q->orWhere('location', 'LIKE', "$k:%");
                    }
                })
                ->get()
                ->groupBy(fn(Word $w) => implode(':', array_slice(explode(':', $w->location), 0, 2)));

            $built = [];
            foreach ($chapterVerses as $v) {
                $vk = $v->verse_key;
                $built[] = [
                    'verse_key'   => $vk,
                    'text_uthmani'=> $v->text_uthmani,
                    'words'       => ($words[$vk] ?? collect())->map(fn($w) => [
                        'location'   => $w->location,
                        'word_index' => (int) explode(':', $w->location)[2],
                        'text'       => $w->text
                    ])->values(),
                ];
            }

            $results[] = [
                'id'          => $chapter->id,
                'name_simple' => $chapter->name_simple,
                'verses'      => $built,
            ];
        }

        $payload = ['search' => ['search_query' => $query]];
        if (count($results) === 1) {
            $payload['search']['result'] = $results[0];
        } else {
            $payload['search']['results'] = $results;
        }

        return response()->json($payload);
    }

    protected function searchJuzWords(string $juz, string $query)
    {
        $verses = Verses::where('juz_number', $juz)->get();
        if ($verses->isEmpty()) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Juz hanya terdiri dari 1 hingga 30'
            ], 404);
        }

        $versesByChapter = $verses->groupBy(fn(Verses $v) => explode(':', $v->verse_key)[0]);

        $results = [];
        foreach ($versesByChapter as $chapterId => $chapterVerses) {
            $chapter = Chapter::find((int)$chapterId);
            if (! $chapter) continue;

            $keys = $chapterVerses->pluck('verse_key')->toArray();
            $words = Word::where(function($q) use ($keys) {
                    foreach ($keys as $k) {
                        $q->orWhere('location', 'LIKE', "$k:%");
                    }
                })
                ->get()
                ->groupBy(fn(Word $w) => implode(':', array_slice(explode(':', $w->location), 0, 2)));

            $built = [];
            foreach ($chapterVerses as $v) {
                $vk = $v->verse_key;
                $built[] = [
                    'verse_key'   => $vk,
                    'text_uthmani'=> $v->text_uthmani,
                    'words'       => ($words[$vk] ?? collect())->map(fn($w) => [
                        'location'   => $w->location,
                        'word_index' => (int) explode(':', $w->location)[2],
                        'text'       => $w->text
                    ])->values(),
                ];
            }

            $results[] = [
                'id'          => $chapter->id,
                'name_simple' => $chapter->name_simple,
                'verses'      => $built,
            ];
        }

        $payload = [
            'search' => [
                'search_query' => $query,
                'juz_number'   => (int)$juz,
            ]
        ];
        if (count($results) === 1) {
            $payload['search']['result'] = $results[0];
        } else {
            $payload['search']['results'] = $results;
        }

        return response()->json($payload);
    }

    protected function searchSpecificVerse(string $verseKey, string $query)
    {
        [$chapterId,] = explode(':', $verseKey);

        $verse = Verses::where('verse_key', $verseKey)->first();
        if (! $verse) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Ayat tidak ditemukan'
            ], 404);
        }

        $chapter = Chapter::find((int)$chapterId);
        if (! $chapter) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Surat tidak ditemukan'
            ], 404);
        }

        $words = Word::where('location', 'LIKE', "$verseKey:%")
            ->get()
            ->map(fn($w) => [
                'location'   => $w->location,
                'word_index' => (int) explode(':', $w->location)[2],
                'text'       => $w->text,
            ]);

        return response()->json([
            'search' => [
                'search_query' => $query,
                'result'       => [
                    'id'          => $chapter->id,
                    'name_simple' => $chapter->name_simple,
                    'verses'      => [[
                        'verse_key'    => $verse->verse_key,
                        'text_uthmani' => $verse->text_uthmani,
                        'words'        => $words
                    ]]
                ]
            ]
        ]);
    }

    protected function searchSuratById(int $id, string $query)
    {
        $chapter = Chapter::find($id);
        if (! $chapter) {
            return response()->json([
                'search_query' => $query,
                'message'      => 'Not found'
            ], 404);
        }

        $verses = Verses::where('verse_key', 'LIKE', "$id:%")
            ->get()
            ->keyBy('verse_key');

        $words = Word::where('location', 'LIKE', "$id:%")->get();

        $grouped = $words->groupBy(fn($w) => implode(':', array_slice(explode(':', $w->location), 0, 2)));

        $built = [];
        foreach ($verses as $vk => $v) {
            $built[] = [
                'verse_key'   => $vk,
                'text_uthmani'=> $v->text_uthmani,
                'words'       => ($grouped[$vk] ?? collect())->map(fn($w) => [
                    'location'   => $w->location,
                    'word_index' => (int) explode(':', $w->location)[2],
                    'text'       => $w->text
                ])->values(),
            ];
        }

        return response()->json([
            'search' => [
                'search_query' => $query,
                'result'       => [
                    'id'          => $chapter->id,
                    'name_simple' => $chapter->name_simple,
                    'verses'      => $built
                ]
            ]
        ]);
    }

    protected function formatSurat(Chapter $chapter): array
    {
        $words = Word::where('location', 'LIKE', "{$chapter->id}:%")->get();
        return [
            'id'              => $chapter->id,
            'name_simple'     => $chapter->name_simple,
            'name_complex'    => $chapter->name_complex,
            'translated_name' => $chapter->translated_name,
            'words'           => $words->map(fn($w) => [
                'location' => $w->location,
                'text'     => $w->text
            ])
        ];
    }
}
