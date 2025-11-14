<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';
    protected $primaryKey = 'comment_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'comment_id',
        'post_id',
        'user_id',
        'content',
        'created_dt',
    ];

    // Relasi ke post
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    // Relasi ke user (author comment)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

