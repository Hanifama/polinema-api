<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $primaryKey = 'tenant_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'tencat_id',
        'name',
        'banner',
        'address',
        'about',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'lat',
        'lng',
        'status',
        'created_dt',
        'created_by'
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = 'tenant-' . Str::uuid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(TenantCategory::class, 'tencat_id', 'tencat_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'tenant_id', 'tenant_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
