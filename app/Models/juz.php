<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class juz extends Model
{
    protected $casts = [
        "verse_mapping"=>"array"
    ];
}
