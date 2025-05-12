<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class setoranRekap extends Model
{
   protected $table = 'qu_setoran_rekap';
//    protected $connection = 'mysql2';

   protected $fillable = [
        'periode',
        'negara',
        'provinsi',
        'kota',
        'lat',
        'long',
        't1',
        't2',
        't3',
        't4',
        't5',
        't6',
        't7',
        't8',
        't9',
        't10',
        't11',
        't12',
        't13',
        't14',
        't15',
        't16',
        't17',
        't18',
        't19',
        't20',
        't21',
        't22',
        't23',
        't24',
        't25',
        't26',
        't27',
        't28',
        't29',
        't30',
        't31',
    ];

    public $timestamps = false;
}
