<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    protected $table = 'friend'; 
    public $timestamps = false; 
    protected $primaryKey = null;
    public $incrementing = false;
    
    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'created_dt',
        'accepted_dt',
        'status',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_1', 'user_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_2', 'user_id');
    }
}
