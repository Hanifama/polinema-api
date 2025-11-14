<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $table = 'banner';
    protected $primaryKey = 'banner_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'banner_id',
        'image',
        'date_from',
        'date_to',
        'status',
        'content',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->banner_id)) {
                $model->banner_id = 'banner-' . Str::uuid();
            }
        });
    }
}
