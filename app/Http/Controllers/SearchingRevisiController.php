<?php

namespace App\Http\Controllers;

use App\Models\chapter;
use App\Models\verses;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SearchingRevisiController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = trim($request->input('q'));
            if (empty($query)) {
                return response()->json([
                    'query' => $query,
                    'results' => [],
                    'message' => 'Query tidak boleh kosong',
                ], 400);
            }

            $surahResults = [];
            $pageResults = [];
            $juzResults = [];

            if (preg_match('/^\d+:\d+$/', $query)) {
                return $this->searchSpecificVerse($query, $query);
            }

            if (preg_match('/^page\s*(\d+)$/i', $query, $m) || preg_match('/^halaman\s*(\d+)$/i', $query, $m)) {
                $pageResults = array_merge($pageResults, $this->getVersesByPage((int)$m[1]));
            } elseif (is_numeric($query)) {
                $pageResults = array_merge($pageResults, $this->getVersesByPage((int)$query));
                $juzResults = array_merge($juzResults, $this->getVersesByJuz((int)$query));
                $surahResults = array_merge($surahResults, $this->resolveSurahResultsById((int)$query));
            }

            if (preg_match('/^juz\s*(\d+)$/i', $query, $m)) {
                $juzResults = array_merge($juzResults, $this->getVersesByJuz((int)$m[1]));
            }

            $similarSurahs = $this->resolveSurahResultsByName($query);

            $existingIds = collect($surahResults)->pluck('id')->toArray();
            $similarSurahs = collect($similarSurahs)
                ->filter(fn ($item) => !empty($item['id']) && !in_array($item['id'], $existingIds))
                ->unique('id')
                ->values()
                ->toArray();

            $surahResults = array_merge($surahResults, $similarSurahs);

            $results = array_merge(
                array_filter($surahResults, fn ($item) => !empty($item)),
                $this->formatGroupResults($pageResults),
                $this->formatGroupResults($juzResults)
            );

            if (empty($results)) {
                return response()->json([
                    'query' => $query,
                    'results' => [],
                    'message' => 'Tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'query' => $query,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), [
                'query' => $query,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'query' => $query,
                'results' => [],
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    protected function searchSpecificVerse(string $verseKey, string $query)
    {
        try {
            $verse = verses::where('verse_key', $verseKey)->first();
            if (!$verse) {
                return response()->json([
                    'query' => $query,
                    'results' => [],
                    'message' => 'Ayat tidak ditemukan',
                ], 404);
            }

            $chapterId = (int) explode(':', $verseKey)[0];
            $chapter = chapter::find($chapterId);
            if (!$chapter) {
                return response()->json([
                    'query' => $query,
                    'results' => [],
                    'message' => 'Surat tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'query' => $query,
                'results' => [[
                    'type' => 'ayat',
                    'chapter_id' => $chapter->id,
                    'name' => $chapter->name_simple ?? '',
                ]],
            ]);
        } catch (\Exception $e) {
            Log::error('searchSpecificVerse error: ' . $e->getMessage(), [
                'verseKey' => $verseKey,
                'query' => $query,
            ]);
            return response()->json([
                'query' => $query,
                'results' => [],
                'message' => 'Terjadi kesalahan saat mencari ayat',
            ], 500);
        }
    }

    protected function resolveSurahResultsById(int $id): array
    {
        try {
            $chapter = chapter::find($id);
            return $chapter ? [$this->getSurahDetail($chapter->id)] : [];
        } catch (\Exception $e) {
            Log::error('resolveSurahResultsById error: ' . $e->getMessage(), [
                'id' => $id,
            ]);
            return [];
        }
    }

    protected function resolveSurahResultsByName(string $query): array
    {
        try {
            $results = [];
            $allMatches = collect();
            $ql = strtolower(trim(preg_replace('/^surah\s+/i', '', $query)));
            $ql = str_replace(['-', ' '], '', $ql);

            $matches = Chapter::whereRaw('LOWER(REPLACE(REPLACE(name_simple, "-", ""), " ", "")) LIKE ?', ['%' . $ql . '%'])
                ->orWhereRaw('LOWER(REPLACE(REPLACE(name_complex, "-", ""), " ", "")) LIKE ?', ['%' . $ql . '%'])
                ->orWhereRaw('LOWER(REPLACE(REPLACE(nama_alt, "-", ""), " ", "")) LIKE ?', ['%' . $ql . '%'])
                ->get();
            if ($matches->count()) {
                $allMatches = $allMatches->concat($matches);
            }

            if ($allMatches->isNotEmpty()) {
                $allMatches = $allMatches->sortBy('id')->values();
                foreach ($allMatches as $chap) {
                    $results[] = [
                        'type' => 'surah',
                        'id' => $chap->id,
                        'name' => $chap->name_simple
                    ];
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('resolveSurahResultsByName error: ' . $e->getMessage(), [
                'query' => $query,
            ]);
            return [];
        }
    }

    protected function getSurahDetail(int $id): array
    {
        try {
            $chapter = chapter::find($id);
            if (!$chapter) {
                return [];
            }
            return [
                'type' => 'surah',
                'id' => $chapter->id,
                'name' => $chapter->name_simple ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('getSurahDetail error: ' . $e->getMessage(), [
                'id' => $id,
            ]);
            return [];
        }
    }

    protected function getVersesByPage(int $page): array
    {
        try {
            $verses = verses::where('page_number', $page)->get();
            return $this->groupVersesWithoutWords($verses, 'page', $page);
        } catch (\Exception $e) {
            Log::error('getVersesByPage error: ' . $e->getMessage(), [
                'page' => $page,
            ]);
            return [];
        }
    }

    protected function getVersesByJuz(int $juz): array
    {
        try {
            $verses = verses::where('juz_number', $juz)->get();
            return $this->groupVersesWithoutWords($verses, 'juz', $juz);
        } catch (\Exception $e) {
            Log::error('getVersesByJuz error: ' . $e->getMessage(), [
                'juz' => $juz,
            ]);
            return [];
        }
    }

    protected function groupVersesWithoutWords($verses, string $type, $value): array
    {
        try {
            if ($verses->isEmpty()) {
                return [];
            }
            $res = [];
            $grouped = $verses->groupBy(fn ($v) => explode(':', $v->verse_key)[0]);
            foreach ($grouped as $cid => $vs) {
                $c = chapter::find((int) $cid);
                if (!$c) {
                    continue;
                }
                $res[] = [
                    'type' => $type,
                    'id' => $value,
                    'chapter_id' => $c->id,
                    'name' => $c->name_simple ?? '',
                ];
            }
            return $res;
        } catch (\Exception $e) {
            Log::error('groupVersesWithoutWords error: ' . $e->getMessage(), [
                'type' => $type,
                'value' => $value,
            ]);
            return [];
        }
    }

    protected function formatGroupResults(array $results): array
    {
        try {
            return collect($results)
                ->groupBy(fn ($r) => "{$r['type']}_{$r['id']}")
                ->map(fn ($items) => [
                    'type' => $items[0]['type'] ?? '',
                    'id' => $items[0]['id'] ?? 0,
                    'name' => $items->pluck('name')->unique()->values()->toArray(),
                ])
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('formatGroupResults error: ' . $e->getMessage());
            return [];
        }
    }
}
