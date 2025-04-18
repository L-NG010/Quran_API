<?php

namespace App\Http\Controllers;

use App\Models\chapter;
use App\Models\ScriptText;
use App\Models\verses;
use Illuminate\Http\Request;

class SearchingController extends Controller
{
    public function searchChapters(Request $request)
    {
        $query = $request->input('q');

        if (preg_match('/^\d+:\d+$/', $query)) {
            $verse = verses::where('verse_key', $query)->first();

            if (!$verse) {
                return response()->json([
                    'message' => 'verse not found'
                ], 404);
            }

            $script = ScriptText::where('verse_key', $query)->first();

            return response()->json([
                'verse_key' => $verse->verse_key,
                'script_text' => $script ? $script->text_imlaei : null
            ]);
        }

        if (preg_match('/^halaman (\d+)$/i', $query,$matches)) {
            $pageNumber = $matches[1];

            $verses = verses::where('page_number', $pageNumber)->get();

            if ($verses->isEmpty()) {
                return response()->json([
                    'message' => 'No verses found for the specified page'
                ], 404);
            }

            $formattedVerses = $verses->map(function($verse) {
                return [
                    'verse_key' => $verse->verse_key,
                    'text_arab' => $verse->teks_arab,
                    'juz_number' => $verse->juz_number,
                    'page_number' => $verse->page_number,
                    'text_uthmani' => $verse->text_uthmani,
                ];
            });

            return response()->json([
                'page_number' => $pageNumber,
                'verses' => $formattedVerses
            ]);
        }

        if (preg_match('/^juz (\d+)$/i', $query,$matches)) {
            $juzNumber = $matches[1];

            $verses = verses::where('juz_number', $juzNumber)->get();

            if ($verses->isEmpty()) {
                return response()->json([
                    'message' => 'No verses found for the specified Juz'
                ], 404);
            }

            $formattedVerses = $verses->map(function($verse) {
                return [
                    'verse_key' => $verse->verse_key,
                    'text_arab' => $verse->teks_arab,
                    'juz_number' => $verse->juz_number,
                    'page_number' => $verse->page_number,
                    'text_uthmani' => $verse->text_uthmani
                ];
            });

            return response()->json([
                'page_number' => $juzNumber,
                'verses' => $formattedVerses
            ]);
        }

        $chapters = chapter::where('name_simple', 'LIKE', '%' . $query . '%')
                            ->orWhere('name_complex', 'LIKE', '%' . $query . '%')
                            ->orWhere('translated_name', 'LIKE', '%' . $query . '%')
                            ->get();

        if ($chapters->isEmpty()) {
            return response()->json([
                'message' => 'chapter not found'
            ], 404);
        }

        $formattedChapters = $chapters->map(function ($chapter) {
            $script = ScriptText::where('verse_key', 'LIKE', $chapter->id . ':%')->get();

            $formattedScripts = $script->map(function ($script) {
                return [
                    'verse_key' => $script->verse_key,
                    'text_indopak' => $script->text_imlaei,
                ];
            });

            return [
                'id' => $chapter->id,
                'name_simple' => $chapter->name_simple,
                'name_complex' => $chapter->name_complex,
                'translated_name' => json_decode($chapter->translated_name),
                'scripts' => $formattedScripts
            ];
        });

        return response()->json($formattedChapters);
    }
}
