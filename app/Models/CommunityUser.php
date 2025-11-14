<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityUser extends Model
{
    protected $table = 'community_user';
    public $incrementing = false;
    public $timestamps = false; 

    protected $primaryKey = null;

    protected $fillable = [
        'community_id',
        'user_id',
        'joined_dt',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }
}