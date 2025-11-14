<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $table = 'communities';
    protected $primaryKey = 'community_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'community_id',
        'name',
        'description',
        'logo',
        'created_dt',
        'category_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->community_id)) {
                $model->community_id = 'comun-' . (string) Str::uuid();
            }

            if (empty($model->created_dt)) {
                $model->created_dt = now(); 
            }
        });
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_user', 'community_id', 'user_id')
            ->withPivot('joined_dt', 'role');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'community_id', 'community_id');
    }
}
