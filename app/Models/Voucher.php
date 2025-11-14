<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $primaryKey = 'voucher_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'voucher_id',
        'vocat_id',
        'tenant_id',
        'title',
        'description',
        'banner_1',
        'banner_2',
        'banner_3',
        'banner_4',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'start_dt',
        'end_dt',
        'is_claimed',
        'quota',
        'used',
        'created_dt',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->voucher_id)) {
                $model->voucher_id = 'voucher-' . Str::uuid();
            }
        });
    }

    protected $appends = ['valid_until', 'validity_info'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    public function category()
    {
        return $this->belongsTo(VoucherCategory::class, 'vocat_id', 'vocat_id');
    }    

    public function getValidUntilAttribute()
    {
        return Carbon::parse($this->end_dt)->translatedFormat('d F Y');
    }

    public function getValidityInfoAttribute()
    {
        $now = \Carbon\Carbon::now();
        $end = \Carbon\Carbon::parse($this->end_dt);

        if ($end->isPast()) {
            return 'Sudah berakhir';
        }

        $diffInMinutes = $now->diffInMinutes($end);
        $diffInHours = floor($diffInMinutes / 60);
        $remainingMinutes = $diffInMinutes % 60;

        if ($diffInMinutes < 60) {
            return "Berakhir dalam $diffInMinutes menit";
        } elseif ($diffInMinutes < 1440) { // kurang dari 1 hari
            if ($remainingMinutes === 0) {
                return "Berakhir dalam $diffInHours jam";
            }
            return "Berakhir dalam $diffInHours jam $remainingMinutes menit";
        }

        $diffInDays = ceil($diffInMinutes / 1440);
        return "Berakhir dalam $diffInDays hari";
    }
}
