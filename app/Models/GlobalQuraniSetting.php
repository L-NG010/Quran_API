<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalQuraniSetting extends Model
{
    // protected $connection = 'mysql2';
    protected $table = 'qu_setting_global';
    protected $fillable = ['key', 'status', 'value'];

    // Untuk mengubah status menjadi aktif atau nonaktif
    public function toggleStatus()
    {
        $this->status = !$this->status; // Toggle antara 1 dan 0
        $this->save();
    }
}
