<?php

namespace App\Http\Controllers;

use App\Models\juz;
use App\Models\verses;
use Illuminate\Http\Request;

class JuzController extends Controller
{
    public function getAllJuz(){
        return juz::all();
    }

    public function by_juz($noJuz){
        $juz = juz::find($noJuz);

        if($juz == null){
            return response()->json([
                'message'=>'juz hanya sampai 30'
            ]);
        }

        $verses = verses::where('juz_number',$noJuz)->get();

        return response()->json(data: [
            "result"=>$juz,
            'scripts'=>$verses
        ]);
    }
}
