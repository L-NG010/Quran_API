<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupQuraniSetting extends Model
{
    // protected $connection = 'mysql2';

    protected $table = 'qu_setting_group';

    protected $fillable = ['group','setting','value','status'];
    public $timestamps = false;
}
