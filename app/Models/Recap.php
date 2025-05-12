<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recap extends Model
{
    // protected $connection = 'mysql2';
    protected $table = 'recap';
    protected $fillable = [
        'nama_peserta',
        'nama_penyimak',
        'kesimpulan_utama',
        'catatan_utama',
        'salah_ayat',
        'salah_kata',
        'kesimpulan_per_halaman',
        'awal_surat',
        'awal_ayat',
        'akhir_surat',
        'akhir_ayat',
        'awal_halaman',
        'akhir_halaman',
    ];

    protected $casts = [
        'salah_ayat' => 'array',
        'salah_kata' => 'array',
        'kesimpulan_per_halaman' => 'array',
    ];

    public $timestamps = true;
}
