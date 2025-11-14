<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserExperience extends Model
{
    use HasFactory;

    protected $table = 'user_experiences';
    protected $primaryKey = 'experience_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'experience_id',
        'user_id',
        'company_name',
        'company_category',
        'position',
        'start_year',
        'end_year',
        'description',
        'created_dt',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($userExperience) {
            if (!$userExperience->experience_id) {
                $userExperience->experience_id = 'experience-' . (string) Str::uuid();
            }
            if (!$userExperience->created_dt) {
                $userExperience->created_dt = Carbon::now();
            }
        });
    }
}
