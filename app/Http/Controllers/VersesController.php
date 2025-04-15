<?php

namespace App\Http\Controllers;

use App\Models\verses;
use Illuminate\Http\Request;

class VersesController extends Controller
{
    public function getAllVerses(){
        return verses::all();
    }
    public function by_page($noPage){
        $verses=verses::where('page_number',$noPage)->get();

        if($noPage > 604){
            return response()->json([
                'message'=>'halaman hanya sampai 604'
            ]);
        }

        return response()->json(data: [
            "result"=>$verses
        ]);
    }


    public function by_verses($verse_key){
        $verses= verses::where('verse_key',$verse_key)->first();

        if($verses == null){
            return response()->json([
                'message'=>'format verse adalah no_surah:no_ayah'
            ]);
        }

        return response()->json([
            'verse'=>$verses
        ]);
    }

    public function byChapter($noChapter){
        $verses=verses::find($noChapter);
        if(!$verses){
            return response()->json([
                'message'=>' aku kesel cok'
            ]);
        }

        return response()->json([
            'verses'=>$verses
        ]);
    }
}
