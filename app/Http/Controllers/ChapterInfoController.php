<?php

namespace App\Http\Controllers;

use App\Models\ChapterInfo;
use Illuminate\Http\Request;

class ChapterInfoController extends Controller
{
    public function getChapterInfo($noChapter){
        $ChapterInfo=ChapterInfo::find($noChapter)->first();

        if(!$ChapterInfo){
            return response()->json([
                'message'=>'chapter info not found'
            ]);
        }

        return response()->json([
            'chapter_info'=>$ChapterInfo
            
        ]);
    }
}
