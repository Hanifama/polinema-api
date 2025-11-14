<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class DiscussionCommentLike extends Model
{
    protected $table = 'discussion_comment_like';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'user_id', 'comment_id', 'created_dt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'like-' . Str::uuid(); 
            }
        });
    }

    public function comment()
    {
        return $this->belongsTo(DiscussionComment::class, 'comment_id', 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
