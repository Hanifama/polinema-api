<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMaster extends Model
{
    protected $table = 'system_master';

    public $timestamps = false; 

    protected $fillable = [
        'category',
        'sub_category',
        'key',
        'value',
        'description',
        'status',
        'created_dt',
        'created_by',
    ];
}
