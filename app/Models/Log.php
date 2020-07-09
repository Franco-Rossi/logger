<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('constants.CONFIG.TABLE_PREFIX') . "logs_data";
    }
}
