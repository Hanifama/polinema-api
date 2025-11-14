<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $table = 'app_versions';

    protected $fillable = [
        'version_code',
        'platform',
        'version_name',
        'version_description',
        'is_latest',
        'is_allowed',
        'released_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_latest' => 'boolean',
        'is_allowed' => 'boolean',
        'released_date' => 'datetime',
    ];
}
