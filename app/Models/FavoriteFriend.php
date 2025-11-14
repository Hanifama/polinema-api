<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteFriend extends Model
{
    // Tentukan nama tabel yang digunakan
    protected $table = 'favorite_friends';

    // Tentukan primary key (karena kita pakai 'favorite_id' sebagai primary key)
    protected $primaryKey = 'favorite_id';

    // Tentukan bahwa favorite_id adalah tipe string (bukan auto increment)
    protected $keyType = 'string';

    public $timestamps = false;

    // Agar ID tidak otomatis disertakan dalam atribut yang dapat diisi
    public $incrementing = false;

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'favorite_id', // ID favorit
        'user_id', // Yang memfavoritkan
        'friend_user_id', // Yang difavoritkan
    ];

    // Relasi dengan user (user yang memfavoritkan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi dengan teman yang difavoritkan
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_user_id', 'user_id');
    }
}

