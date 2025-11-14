<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Article extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $dates = [
        'published_dt'
    ];

    protected $fillable = [
        'article_id',
        'title',
        'slug',
        'content',
        'image',
        'author',
        'published_by',
        'published_dt'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $uuid = Str::uuid();
                $model->{$model->getKeyName()} = 'article-' . $uuid;
            }

            if (empty($model->published_dt)) {
                $model->published_dt = Carbon::now();
            }
        });
    }
}
