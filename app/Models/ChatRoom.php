<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_room';
    protected $primaryKey = 'chroom_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'chroom_id',
        'title',
        'chat_type',
        'image',
        'created_dt',
        'created_by',
        'status',
        'member_cnt',
        'allow_invite',
        'only_admin'
    ];

    // Relasi
    public function members()
    {
        return $this->hasMany(ChatMember::class, 'chroom_id', 'chroom_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chroom_id', 'chroom_id');
    }

    public function isUserMember($user_id)
    {
        return $this->members()->where('user_id', $user_id)->exists();
    }

    // Scope
    public function scopePrivate($query)
    {
        return $query->where('chat_type', 'private');
    }

    // Cek jika private room ada
    public static function checkIfPrivateRoomExists($userA, $userB)
    {
        $users = [$userA, $userB];
        sort($users);
        $identifier = implode('-', $users);

        return self::private()->where('title', $identifier)->exists();
    }

    // Get atau buat room baru untuk dua user
    public static function getOrCreatePrivateRoom($userA, $userB, &$wasCreated = false)
    {
        $users = [$userA, $userB];
        sort($users);
        $identifier = implode('-', $users);

        $room = self::private()->where('title', $identifier)->first();

        if (!$room) {
            $room = self::create([
                'title' => $identifier,
                'chat_type' => 'private',
                'created_dt' => now(),
                'created_by' => $userA,
                'status' => 'active',
                'member_cnt' => 2,
            ]);

            foreach ($users as $uid) {
                ChatMember::firstOrCreate([
                    'chroom_id' => $room->chroom_id,
                    'user_id' => $uid,
                ]);
            }

            $wasCreated = true; 
        }

        return $room;
    }


    // Auto-generate UUID
    protected static function booted()
    {
        static::creating(function ($chatRoom) {
            if (!$chatRoom->chroom_id) {
                $chatRoom->chroom_id = 'room-' . (string) Str::orderedUuid();
            }
        });
    }
}
