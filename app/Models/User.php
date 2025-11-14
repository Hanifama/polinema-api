<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"user_id", "name", "email", "password", "role"},
 *     @OA\Property(
 *         property="user_id",
 *         type="string",
 *         description="ID Pengguna (unik untuk setiap pengguna)"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nama Pengguna"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email Pengguna"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Password Pengguna"
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         description="URL Foto Profil Pengguna"
 *     ),
 *     @OA\Property(
 *         property="major",
 *         type="string",
 *         description="Jurusan Pengguna"
 *     ),
 *     @OA\Property(
 *         property="year_generation",
 *         type="string",
 *         description="Tahun Angkatan Pengguna"
 *     ),
 *     @OA\Property(
 *         property="job",
 *         type="string",
 *         description="Pekerjaan Pengguna"
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string",
 *         description="Lokasi Pengguna"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status Pengguna (Aktif/Non-aktif)"
 *     ),
 *     @OA\Property(
 *         property="lat",
 *         type="number",
 *         format="float",
 *         description="Latitude Lokasi Pengguna"
 *     ),
 *     @OA\Property(
 *         property="lng",
 *         type="number",
 *         format="float",
 *         description="Longitude Lokasi Pengguna"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Peran Pengguna (admin, user, dll)"
 *     ),
 *     @OA\Property(
 *         property="otp_code",
 *         type="string",
 *         description="Kode OTP Pengguna"
 *     ),
 *     @OA\Property(
 *         property="otp_expires_at",
 *         type="string",
 *         format="date-time",
 *         description="Waktu Kadaluarsa OTP"
 *     ),
 *     @OA\Property(
 *         property="created_dt",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal Dibuatnya Pengguna"
 *     )
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'photo',
        'major',
        'year_generation',
        'job',
        'location',
        'province',
        'status',
        'lat',
        'lng',
        'is_share_location',
        'role',
        'marital_status',
        'dependents',
        'phone_number',
        'otp_code',
        'otp_expires_at',
        'created_dt'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_dt' => 'datetime',
        'otp_expires_at' => 'datetime',
        'dependents' => 'integer',
    ];

    public function getCreatedDtAttribute($value)
    {
        return Carbon::parse($value)
            ->setTimezone('Asia/Jakarta')
            ->format('Y-m-d H:i');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getMaritalStatusAttribute($value)
    {
        return ucfirst($value);
    }

    public function experiences()
    {
        return $this->hasMany(UserExperience::class, 'user_id', 'user_id');
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'major' => $this->major,
            'year_generation' => $this->year_generation,
            'job' => $this->job,
            'photo' => $this->photo,
            'location' => $this->location,
            'status' => $this->status,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'created_dt' => $this->created_dt
        ];
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_user', 'user_id', 'community_id')
            ->withPivot('joined_dt');
    }
}
