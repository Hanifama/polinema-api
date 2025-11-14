<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiscussionComment extends Model
{
    protected $table = 'discussion_comment';
    protected $primaryKey = 'comment_id';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'comment_id',
        'user_id',
        'discus_id',
        'created_dt',
        'content',
        'attachment',
        'attachment_mime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->comment_id)) {
                $model->comment_id = 'comment-' . Str::uuid();
            }
        });
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke diskusi
    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'discus_id', 'discus_id');
    }

    //relasi ke like komen
    public function likes()
    {
        return $this->hasMany(DiscussionCommentLike::class, 'comment_id', 'comment_id');
    }
}
