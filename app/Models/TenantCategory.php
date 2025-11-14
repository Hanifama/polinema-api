<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TenantCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'tencat_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tencat_id', 'name', 'icon', 'status'];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tencat_id)) {
                $model->tencat_id = 'tencat-' . Str::uuid();
            }
        });
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'tencat_id', 'tencat_id');
    }
}
