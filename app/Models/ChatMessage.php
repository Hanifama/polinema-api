<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_message';
    protected $primaryKey = ['user_id', 'chroom_id', 'created_dt']; 
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['user_id', 'chroom_id', 'message', 'attachment', 'attachment_mime', 'created_dt', 'is_deliver', 'is_read'];

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

    public static function getMessagesForUser($chroom_id, $user_id)
    {
        // Cek apakah user tersebut bagian dari chat room
        $isUserInRoom = ChatMember::where('chroom_id', $chroom_id)
            ->where('user_id', $user_id)
            ->exists();

        if ($isUserInRoom) {
            // Ambil semua pesan yang ada di chat room untuk user tersebut
            return self::where('chroom_id', $chroom_id)
                ->whereIn('user_id', function ($query) use ($chroom_id) {
                    $query->select('user_id')
                        ->from('chat_member')
                        ->where('chroom_id', $chroom_id);
                })
                ->orderBy('created_dt', 'asc')
                ->get();
        }

        return [];
    }
}
