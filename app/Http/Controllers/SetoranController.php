<?php

namespace App\Http\Controllers;

use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SetoranController extends Controller
{
    public function getSetoran(Request $r){
        $r->validate([
            'username' => [
                'required',
                Rule::exists('mysql2.setoran', 'user_name') // 👈 koneksi.mysql_table, kolom
            ]
        ]);
        $setoran=Setoran::where('user_name',$r->username)->latest()->first();

        return response()->json([
            'username'=>$setoran->user_name,
            'penynimak'=>$setoran->penyimak_name,
            'type'=>$setoran->penyimak_type
        ]);
    }
}
