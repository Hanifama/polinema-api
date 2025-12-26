<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostReport extends Model
{
    protected $table = 'post_reports';

    protected $primaryKey = 'report_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // custom timestamp (created_dt & updated_dt)
    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'report_type',
        'reported_discus_id',
        'reported_post_id',
        'reporter_user_id',
        'category_code',
        'description',
        'status',
        'admin_note',
        'resolved_by',
        'created_dt',
        'updated_dt',
    ];

    /**
     * Auto generate ID & timestamp
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->report_id ??= 'report-' . Str::uuid();
            $model->created_dt ??= now();
        });

        static::updating(function ($model) {
            $model->updated_dt = now();
        });
    }

    /**
     * Relasi ke Discussion
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'reported_discus_id', 'discus_id');
    }

    /**
     * Relasi ke Post Wall
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'reported_post_id', 'post_id');
    }

    /**
     * Relasi ke User (pelapor)
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id', 'user_id');
    }

    /**
     * Helper: cek tipe report
     */
    public function isDiscussionReport(): bool
    {
        return $this->report_type === 'discussion';
    }

    public function isPostReport(): bool
    {
        return $this->report_type === 'post_wall';
    }
}
