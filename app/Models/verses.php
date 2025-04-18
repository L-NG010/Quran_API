<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verses extends Model
{

protected $casts = [
    "translations"=>"array"
];
  protected $table = 'verses';
  protected $fillable = [
    'verse_number', 'verse_key', 'hizb_number', 'rub_el_hizb_number',
    'ruku_number', 'manzil_number', 'sajdah_number', 'page_number', 'juz_number'
];
}
