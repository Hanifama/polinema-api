<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $table = 'post_likes';
    protected $primaryKey = 'like_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'like_id',
        'post_id',
        'user_id',
        'created_dt',
    ];

    // Relasi ke post
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    // Relasi ke user (yang memberi like)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

