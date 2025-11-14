<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class VoucherCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'vocat_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'vocat_id',
        'name',
        'icon',
        'description',
        'created_dt',
    ];

    public $timestamps = false;

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'vocat_id', 'vocat_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->vocat_id)) {
                $model->vocat_id = 'vocat-' . Str::uuid();
            }
        });
    }
}
