<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class chapter extends Model
{
    protected $fillable = [
        'id',
        'revelation_place',
        'revelation_order',
        'bismillah_pre',
        'name_simple',
        'name_complex',
        'name_arabic',
        'verses_count',
        'pages',
        'translated_name'
    ];

    protected $cast = [
        'pages' => 'array',
        'translated_name' => 'array'
    ];
}
