<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BannerTenant extends Model
{
    protected $table = 'banner_tenant';
    protected $primaryKey = 'banner_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'banner_id',
        'image',
        'date_from',
        'date_to',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->banner_id)) {
                $model->banner_id = 'banner-tenant' . Str::uuid();
            }
        });
    }
}
