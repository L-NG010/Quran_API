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
        // Validasi nomor halaman
        if ($noPage > 604 || $noPage < 1) {
            return response()->json([
                'message' => 'Halaman hanya dari 1 sampai 604'
            ], 400);
        }

        // Ambil parameter per_page dan current_page dari query string, default: per_page=10, current_page=1
        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);

        // Hitung offset untuk query
        $offset = ($currentPage - 1) * $perPage;

        // Ambil data verses berdasarkan page_number
        $verses = Verses::where('page_number', $noPage)
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Hitung total records untuk page_number ini
        $totalRecords = Verses::where('page_number', $noPage)->count();

        // Hitung total halaman
        $totalPages = ceil($totalRecords / $perPage);

        // Tentukan next_page
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
        $verses = Verses::where('verse_key', $verse_key)->first();

        if (!$verses) {
            return response()->json([
                'message' => 'Format verse adalah no_surah:no_ayah'
            ], 400);
        }

        return response()->json([
            'verse' => $verses
        ]);
    }

    public function byChapter($noChapter, Request $request)
    {
        // Validasi nomor chapter
        if ($noChapter < 1 || $noChapter > 114) {
            return response()->json([
                'message' => 'Chapter hanya dari 1 sampai 114'
            ], 400);
        }

        // Ambil parameter per_page dan current_page
        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);

        // Hitung offset
        $offset = ($currentPage - 1) * $perPage;

        // Ambil data verses berdasarkan chapter (verse_key LIKE 'noChapter:%')
        $verses = Verses::where('verse_key', 'LIKE', $noChapter . ':%')
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Hitung total records untuk chapter ini
        $totalRecords = Verses::where('verse_key', 'LIKE', $noChapter . ':%')->count();

        // Hitung total halaman
        $totalPages = ceil($totalRecords / $perPage);

        // Tentukan next_page
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
        // Validasi nomor juz
        if ($noJuz < 1 || $noJuz > 30) {
            return response()->json([
                'message' => 'Juz hanya dari 1 sampai 30'
            ], 400);
        }

        // Ambil parameter per_page dan current_page
        $perPage = $request->query('per_page', 10);
        $currentPage = $request->query('page', 1);

        // Hitung offset
        $offset = ($currentPage - 1) * $perPage;

        // Ambil data verses berdasarkan juz_number
        $verses = Verses::where('juz_number', $noJuz)
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Hitung total records untuk juz ini
        $totalRecords = Verses::where('juz_number', $noJuz)->count();

        // Hitung total halaman
        $totalPages = ceil($totalRecords / $perPage);

        // Tentukan next_page
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
