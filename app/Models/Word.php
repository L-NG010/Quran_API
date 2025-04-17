<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $casts = [
        'translation'=>'array',
        'transliteration'=>'array'
    ];
}
