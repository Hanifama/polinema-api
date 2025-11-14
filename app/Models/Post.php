<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts'; 
    protected $primaryKey = 'post_id';
    public $incrementing = false; 
    protected $keyType = 'string';  
    public $timestamps = false;
    protected $fillable = [
        'post_id',
        'wall_owner_id',
        'author_id',
        'content',
        'attachment',
        'type',
        'privacy',
        'created_dt',
        'replied',
    ];

    // Relasi dengan komentar
    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id', 'post_id');
    }

    // Relasi dengan likes
    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id', 'post_id');
    }

    // Relasi dengan Author
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    // Relasi dengan Wall Owner
    public function wallOwner()
    {
        return $this->belongsTo(User::class, 'wall_owner_id', 'user_id');
    }
}
