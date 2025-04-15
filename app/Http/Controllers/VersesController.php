<?php

namespace App\Http\Controllers;

use App\Models\Verses;
use Illuminate\Http\Request;

class VersesController extends Controller
{
    public function getAllVerses()
    {
        return Verses::all();
    }

    public function by_page($noPage, Request $request)
    {
        if ($noPage > 604 || $noPage < 1) {
            return response()->json(['message' => 'Halaman hanya dari 1 sampai 604'], 400);
        }

        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $verses = Verses::where('page_number', $noPage)->skip($offset)->take($perPage)->get();
        $totalRecords = Verses::where('page_number', $noPage)->count();
        $totalPages = ceil($totalRecords / $perPage);
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page' => (int) $perPage,
                'current_page' => (int) $currentPage,
                'next_page' => $nextPage,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ]
        ]);
    }

    public function by_verses($verse_key)
    {
        $verse = Verses::where('verse_key', $verse_key)->first();
        if (!$verse) {
            return response()->json(['message' => 'Format verse adalah no_surah:no_ayah'], 400);
        }
        return response()->json(['verse' => $verse]);
    }

    public function byChapter($noChapter, Request $request)
    {
        if ($noChapter < 1 || $noChapter > 114) {
            return response()->json(['message' => 'Chapter hanya dari 1 sampai 114'], 400);
        }

        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $verses = Verses::where('verse_key', 'LIKE', $noChapter . ':%')->skip($offset)->take($perPage)->get();
        $totalRecords = Verses::where('verse_key', 'LIKE', $noChapter . ':%')->count();
        $totalPages = ceil($totalRecords / $perPage);
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page' => (int) $perPage,
                'current_page' => (int) $currentPage,
                'next_page' => $nextPage,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ]
        ]);
    }

    public function byJuz($noJuz, Request $request)
    {
        if ($noJuz < 1 || $noJuz > 30) {
            return response()->json(['message' => 'Juz hanya dari 1 sampai 30'], 400);
        }

        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $verses = Verses::where('juz_number', $noJuz)->skip($offset)->take($perPage)->get();
        $totalRecords = Verses::where('juz_number', $noJuz)->count();
        $totalPages = ceil($totalRecords / $perPage);
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        return response()->json([
            'verses' => $verses,
            'pagination' => [
                'per_page' => (int) $perPage,
                'current_page' => (int) $currentPage,
                'next_page' => $nextPage,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ]
        ]);
    }
}
