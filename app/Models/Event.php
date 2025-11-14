<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $dates = [
        'created_dt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $uuid = Str::uuid();
                $model->{$model->getKeyName()} = 'event-' . $uuid;
            }

            if (empty($model->created_dt)) {
                $model->created_dt = Carbon::now();
            }
        });
    }

    protected $fillable = [
        'event_id',
        'event_name',
        'event_date',
        'location',
        'description',
        'status',
        'created_dt',
    ];
}
