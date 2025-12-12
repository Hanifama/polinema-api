<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserBlocked extends Model
{

    protected $table = 'users_blocked';
    protected $primaryKey = 'blocked_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'blocked_id',
        'blocker_user_id',
        'blocked_user_id',
        'reason',
        'is_active',
        'created_dt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // generate blk_xxxxxx only if empty
            if (empty($model->blocked_id)) {
                $model->blocked_id = 'blocked-' . Str::random(16);
            }
        });
    }

    /**
     * Relasi ke user yang memblokir.
     */
    public function blockerUser()
    {
        return $this->belongsTo(User::class, 'blocker_user_id');
    }

    /**
     * Relasi ke user yang diblokir.
     */
    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
