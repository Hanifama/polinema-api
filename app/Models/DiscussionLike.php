<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionLike extends Model
{
    protected $table = 'discussion_like';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'discus_id',
        'created_dt',
    ];

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
}
