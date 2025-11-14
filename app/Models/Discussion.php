<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Discussion extends Model
{
    protected $table = 'discussion';
    protected $primaryKey = 'discus_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'discus_id',
        'community_id',
        'title',
        'content',
        'attachment',
        'attachment_mime',
        'created_dt',
        'approved_dt',
        'user_id',
        'view_cnt',
        'like_cnt',
        'comment_cnt',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke komentar
    public function comments()
    {
        return $this->hasMany(DiscussionComment::class, 'discus_id', 'discus_id');
    }

    // Relasi ke likes
    public function likes()
    {
        return $this->hasMany(DiscussionLike::class, 'discus_id', 'discus_id');
    }

    // Mutator untuk set discus_id otomatis
    public static function boot()
    {
        parent::boot();

        static::creating(function ($discussion) {
            // Menambahkan discus_id jika belum ada
            if (!$discussion->discus_id) {
                $discussion->discus_id = 'discus-' . Str::uuid();
            }

            // Menambahkan created_dt jika belum ada
            if (!$discussion->created_dt) {
                $discussion->created_dt = Carbon::now();
            }
        });
    }
}
