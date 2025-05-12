<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuraniSetting extends Model
{
    // protected $connection = 'mysql2';
    protected $table = 'qu_setting_user';
    protected $fillable = ['user','setting','value','status'];

}
