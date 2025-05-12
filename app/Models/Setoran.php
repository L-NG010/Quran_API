<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    // protected $connection = 'mysql2';
    protected $table = 'qu_setoran';

    public $timestamps = false;

    protected $fillable = [
        'tgl',
        'penyetor',
        'penerima',
        'setoran',
        'tampilan',
        'nomor',
        'info',
        'hasil',
        'ket',
        'kesalahan',
        'perhalaman',
        'paraf',
        'paraftgl',
        'parafoleh',
    ];

    protected $casts = [
        'tgl' => 'datetime',
        'setoran' => 'string',
        'tampilan' => 'string',
        'hasil' => 'string',
        'kesalahan' => 'array',
        'perhalaman' => 'array',
        'paraftgl' => 'datetime',
    ];

    // Nonaktifkan mass assignment unguarded untuk keamanan
    protected static $unguarded = false;
}
