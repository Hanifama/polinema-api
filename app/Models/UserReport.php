<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserReport extends Model
{
    protected $table = 'user_reports';

    public $timestamps = false;

    protected $primaryKey = 'report_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'report_id',
        'reported_user_id',
        'reporter_user_id',
        'category_code',
        'description',
        'status',
        'admin_note',
        'resolved_by',
        'created_dt',
        'updated_dt',
    ];

    // Auto-generate UUID saat creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->report_id) {
                $model->report_id = (string) Str::uuid();
            }

            $model->created_dt = now();
        });

        static::updating(function ($model) {
            $model->updated_dt = now();
        });
    }

    // Relasi ke User yang dilaporkan
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id', 'user_id');
    }

    // Relasi ke User pelapor
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id', 'user_id');
    }

    // Relasi kategori
    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'category_code', 'code');
    }
}
