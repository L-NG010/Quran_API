<?php

namespace App\Http\Controllers;

use App\Models\chapter;
use App\Models\ScriptText;
use App\Models\verses;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function getAllChapters(){
        $chapter=chapter::all();
        return response()->json([
            'chapters'=>$chapter
        ]);
    }

    public function getByChapter($noChapter){
        $chapter=chapter::find($noChapter);

        if(!$chapter){
            return response()->json([
                'message'=>'chapter not found'
            ]);
        }

        $script = ScriptText::where('verse_key','LIKE',$noChapter.':%')->get();
        $page_number = verses::where('verse_key','LIKE',$noChapter.':%')->pluck('page_number')->first();
        $juz_number = verses::where('verse_key','LIKE',$noChapter.':%')->pluck('juz_number')->first();

        $formattedScripts = $script->map(function($script) {
            return [
                'verse_key' => $script->verse_key,
                'text_indopak' => $script->text_imlaei,
            ];
        });

        return response()->json([
            'id'=>$chapter->id,
            'revelation_place'=>$chapter->revelation_place,
            'revelation_order'=>$chapter->revelation_order,
            'bismillah_pre'=>$chapter->bismillah_pre,
            'name_simple'=>$chapter->name_simple,
            'name_complex'=>$chapter->name_complex,
            'verses_count'=>$chapter->verses_count,
            'page_number'=>$page_number,
            'juz_number'=>$juz_number,
            'translated_name'=>json_decode($chapter->translated_name),
            'scripts'=>$formattedScripts
        ]);
    }
}
