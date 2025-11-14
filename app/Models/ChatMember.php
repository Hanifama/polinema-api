<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    use HasFactory;

    protected $table = 'chat_member';
    protected $primaryKey = ['user_id', 'chroom_id'];  
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['user_id', 'chroom_id', 'joined_dt', 'status'];

    // Relasi ke chat room
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class, 'chroom_id', 'chroom_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
