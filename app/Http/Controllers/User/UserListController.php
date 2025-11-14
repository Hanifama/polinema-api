<?php

namespace App\Http\Controllers\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\FavoriteFriend;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserListController extends Controller
{

    /**
     * @OA\Get(
     *     path="/users-recommendations",
     *     summary="Ambil rekomendasi teman berdasarkan jurusan",
     *     description="Endpoint ini memberikan daftar rekomendasi teman kepada pengguna berdasarkan jurusan yang sama. Hanya menampilkan pengguna yang belum berteman dengan user saat ini. Menggunakan token Bearer yang valid.",
     *     tags={"Temukan Teman"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil rekomendasi teman",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rekomendasi teman berdasarkan jurusan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="string", example="user-uuid"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="major", type="string", example="Informatics Engineering"),
     *                     @OA\Property(property="year_generation", type="string", example="2022"),
     *                     @OA\Property(property="job", type="string", example="Software Developer"),
     *                     @OA\Property(property="location", type="string", example="Jakarta"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/profile.jpg"),
     *                     @OA\Property(property="lat", type="number", format="float", example=-6.200000),
     *                     @OA\Property(property="lng", type="number", format="float", example=106.816666)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token tidak valid atau tidak disediakan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Terjadi kesalahan saat mengambil rekomendasi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statusCode", type="integer", example=500),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Terjadi kesalahan saat mengambil rekomendasi teman."),
     *             @OA\Property(property="errors", type="object", example={"error": "Detail error di sini"})
     *         )
     *     )
     * )
     */
    public function getFriendRecommendations()
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();
            $userMajor = $authUser->major;

            // Ambil semua teman yang sudah accepted
            $friendIds = Friend::where(function ($query) use ($authUser) {
                $query->where('user_id_1', $authUser->user_id)
                    ->orWhere('user_id_2', $authUser->user_id);
            })
                ->where('status', 'accepted')
                ->get()
                ->flatMap(function ($friend) use ($authUser) {
                    return [$friend->user_id_1, $friend->user_id_2];
                })
                ->unique()
                ->reject(fn($id) => $id === $authUser->user_id); // Kecualikan diri sendiri

            // Cari user sejurusan tapi belum berteman
            $recommendations = User::where('major', $userMajor)
                ->where('user_id', '!=', $authUser->user_id)
                ->whereNotIn('user_id', $friendIds)
                ->get()
                ->map(function ($user) {
                    return [
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'major' => $user->major,
                        'year_generation' => $user->year_generation,
                        'job' => $user->job,
                        'location' => $user->location,
                        'photo' => $user->photo,
                        'lat' => $user->lat,
                        'lng' => $user->lng,
                    ];
                });

            if ($recommendations->isEmpty()) {
                return ApiResponse::success('Tidak ada rekomendasi teman berdasarkan jurusan.');
            }

            return ApiResponse::success('Rekomendasi teman berdasarkan jurusan berhasil diambil.', $recommendations);
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan saat mengambil rekomendasi teman.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/favorites",
     *     summary="Mengambil daftar teman favorit",
     *     description="Mengambil daftar teman favorit untuk pengguna yang terautentikasi, termasuk informasi dasar setiap teman seperti nama, foto, jurusan, dan lokasi.",
     *     tags={"Temukan Teman"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar teman favorit",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Permintaan buruk (Bad Request)",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Kesalahan server internal",
     *     )
     * )
     */
    public function getFavorites()
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            // Ambil semua teman favorit milik pengguna
            $favorites = FavoriteFriend::where('user_id', $authUser->user_id)
                ->with('friend') // Asumsi kamu punya relasi 'friend' pada model FavoriteFriend
                ->get()
                ->map(function ($favorite) {
                    return [
                        'favorite_id' => $favorite->favorite_id,
                        'user_id' => $favorite->user_id,
                        'friend_user_id' => $favorite->friend_user_id,
                        'created_dt' => $favorite->created_dt,
                        'friend' => [
                            'name' => $favorite->friend->name,
                            'photo' => $favorite->friend->photo,
                            'major' => $favorite->friend->major,
                            'location' => $favorite->friend->location,
                        ]
                    ];
                });

            if ($favorites->isEmpty()) {
                return ApiResponse::success('Tidak ada teman favorit yang ditemukan.');
            }

            return ApiResponse::success('Daftar teman favorit berhasil diambil.', $favorites);
        } catch (\Exception $e) {
            return ApiResponse::error('Terjadi kesalahan saat mengambil daftar teman favorit.', ['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Ambil data pengguna di sekitar",
     *     description="Menampilkan data pengguna yang sedang login (self) dan daftar pengguna aktif lainnya. Bisa difilter dengan parameter:
     * - 'major' untuk filter jurusan (partial match, case-insensitive)
     * - 'year_generation' untuk filter angkatan (partial match, case-insensitive)
     * - 'search' untuk cari nama pengguna (partial match, case-insensitive)
     * - 'radius' untuk membatasi pencarian berdasarkan jarak dari lokasi pengguna (dalam kilometer)",
     *     tags={"Temukan Teman"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="major",
     *         in="query",
     *         description="Filter berdasarkan jurusan pengguna (partial match, case-insensitive)",
     *         required=false,
     *         @OA\Schema(type="string", example="Informatika")
     *     ),
     *     @OA\Parameter(
     *         name="year_generation",
     *         in="query",
     *         description="Filter berdasarkan angkatan pengguna (partial match, case-insensitive)",
     *         required=false,
     *         @OA\Schema(type="string", example="2020")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cari pengguna berdasarkan nama (partial match, case-insensitive)",
     *         required=false,
     *         @OA\Schema(type="string", example="Haris")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radius pencarian pengguna lain dari lokasi saat ini (dalam kilometer)",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="community_id",
     *         in="query",
     *         description="Filter pengguna yang tergabung dalam komunitas tertentu (berdasarkan ID komunitas)",
     *         required=false,
     *         @OA\Schema(type="string", example="comun-1a34a18e-a6a2-446c-a385-1156f170fc79")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data pengguna",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data Teman berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="self",
     *                     type="object",
     *                     description="Data pengguna yang sedang login",
     *                     @OA\Property(property="user_id", type="string", example="USR001"),
     *                     @OA\Property(property="name", type="string", example="Haris"),
     *                     @OA\Property(property="photo", type="string", example="https://example.com/foto.jpg"),
     *                     @OA\Property(property="year_generation", type="string", example="2022"),
     *                     @OA\Property(property="major", type="string", example="Teknik Informatika"),
     *                     @OA\Property(property="job", type="string", example="Programmer"),
     *                     @OA\Property(property="location", type="string", example="Jakarta"),
     *                     @OA\Property(property="lat", type="number", format="float", example="-6.200000"),
     *                     @OA\Property(property="lng", type="number", format="float", example="106.816666"),
     *                     @OA\Property(property="created_dt", type="string", format="date-time", example="2024-01-01T12:00:00Z")
     *                 ),
     *                 @OA\Property(
     *                     property="users_around",
     *                     type="array",
     *                     description="Daftar pengguna aktif lainnya",
     *                     @OA\Items(
     *                         @OA\Property(property="user_id", type="string", example="USR002"),
     *                         @OA\Property(property="name", type="string", example="Nadia"),
     *                         @OA\Property(property="photo", type="string", example="https://example.com/foto2.jpg"),
     *                         @OA\Property(property="year_generation", type="string", example="2021"),
     *                         @OA\Property(property="major", type="string", example="Sistem Informasi"),
     *                         @OA\Property(property="job", type="string", example="Network Engineer"),
     *                         @OA\Property(property="location", type="string", example="Depok"),
     *                         @OA\Property(property="lat", type="number", format="float", example="-6.4"),
     *                         @OA\Property(property="lng", type="number", format="float", example="106.8"),
     *                         @OA\Property(property="created_dt", type="string", format="date-time", example="2023-01-01T08:00:00Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated."
     *     )
     * )
     */
    public function index(Request $request)
    {
        $authUser = JWTAuth::parseToken()->authenticate();
        $authUser->makeHidden(['password', 'otp_code', 'otp_expires_at', 'role']);

        $self = [
            'user_id'         => $authUser->user_id,
            'name'            => $authUser->name,
            'photo'           => $authUser->photo,
            'year_generation' => $authUser->year_generation,
            'major'           => $authUser->major,
            'job'             => $authUser->job,
            'location'        => $authUser->location,
            'lat'             => $authUser->lat,
            'lng'             => $authUser->lng,
            'created_dt'      => $authUser->created_dt,
        ];

        $radius = $request->filled('radius') && is_numeric($request->radius)
            ? floatval($request->radius)
            : null;

        $userLat = $authUser->lat;
        $userLng = $authUser->lng;

        $distanceFormula = "(6371 * acos(
                        cos(radians(?)) *
                        cos(radians(CAST(users.lat AS DOUBLE PRECISION))) *
                        cos(radians(CAST(users.lng AS DOUBLE PRECISION)) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(CAST(users.lat AS DOUBLE PRECISION)))
                    ))";

        $query = User::select(
            'users.user_id',
            'users.name',
            'users.photo',
            'users.year_generation',
            'users.major',
            'users.job',
            'users.location',
            'users.lat',
            'users.lng',
            'users.is_share_location',
            'users.created_dt'
        )
            ->selectRaw("$distanceFormula AS distance", [$userLat, $userLng, $userLat])
            ->where('users.user_id', '!=', $authUser->user_id)
            ->where('users.status', 'active');

        // Filter by community_id (optional)
        if ($request->filled('community_id')) {
            $query->join('community_user', function ($join) use ($request) {
                $join->on('users.user_id', '=', 'community_user.user_id')
                    ->where('community_user.community_id', $request->community_id);
            });
        }

        // Filter by radius (optional)
        if ($radius !== null) {
            $query->whereRaw("$distanceFormula <= ?", [$userLat, $userLng, $userLat, $radius]);
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('users.major', 'ILIKE', '%' . $request->major . '%');
        }

        // Filter by year_generation
        if ($request->filled('year_generation')) {
            $query->where('users.year_generation', $request->year_generation);
        }

        // Filter by search (name)
        if ($request->filled('search')) {
            $query->where('users.name', 'ILIKE', '%' . $request->search . '%');
        }

        // Fetch final list with distance info
        $friends = $query
            ->when($radius !== null, function ($q) {
                $q->orderBy('distance');
            }, function ($q) {
                $q->orderBy('created_dt', 'desc');
            })
            ->get()
            ->map(function ($user) {
                $user->is_share_location = (bool) $user->is_share_location; // Pastikan boolean

                if (!$user->is_share_location) {
                    $user->lat = null;
                    $user->lng = null;
                    $user->distance = null;
                    $user->distance_in_km = null;
                    $user->distance_display = 'Lokasi disembunyikan oleh pengguna';
                } else {
                    $distance = floatval($user->distance);

                    $user->distance_in_km = round($distance, 2);
                    $user->distance_display = $distance < 1
                        ? round($distance * 1000) . ' meter'
                        : round($distance, 2) . ' km';
                }

                return $user;
            });

        return response()->json([
            'statusCode' => 200,
            'status'     => true,
            'message'    => 'Data Teman berhasil diambil.',
            'data'       => [
                'self'         => $self,
                'users_around' => $friends,
            ]
        ]);
    }


    /**
     * @OA\Get(
     *     path="/users/{user_id}",
     *     summary="Lihat detail pengguna berdasarkan ID",
     *     description="Endpoint ini digunakan untuk menampilkan detail dari seorang pengguna yang aktif berdasarkan user_id. Jika user_id bukan milik pengguna yang status akunnya aktif, maka akan dihitung jaraknya dari pengguna saat ini.",
     *     tags={"Temukan Teman"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID unik pengguna yang ingin ditampilkan",
     *         required=true,
     *         @OA\Schema(type="string", example="usr-uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil detail pengguna",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detail data berhasil dimuat."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="string", example="usr-uuid"),
     *                 @OA\Property(property="name", type="string", example="Nadia"),
     *                 @OA\Property(property="photo", type="string", example="https://example.com/foto.jpg"),
     *                 @OA\Property(property="year_generation", type="string", example="2021"),
     *                 @OA\Property(property="major", type="string", example="Sistem Informasi"),
     *                 @OA\Property(property="job", type="string", example="Backend Developer"),
     *                 @OA\Property(property="location", type="string", example="Bandung"),
     *                 @OA\Property(property="lat", type="number", format="float", example="-6.914744"),
     *                 @OA\Property(property="lng", type="number", format="float", example="107.609810"),
     *                 @OA\Property(property="created_dt", type="string", format="date-time", example="2024-05-01T10:00:00Z"),
     *                 @OA\Property(property="distance_km", type="number", format="float", example=3.2, nullable=true),
     *                 @OA\Property(property="distance_info", type="string", example="± 3.2 km", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pengguna tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Pengguna tidak ditemukan."),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     )
     * )
     */
    public function show($user_id)
    {
        $authUser = JWTAuth::parseToken()->authenticate();

        $user = User::where('user_id', $user_id)
            ->where('status', 'active')
            ->select(
                'user_id',
                'name',
                'photo',
                'year_generation',
                'major',
                'job',
                'location',
                'lat',
                'lng',
                'created_dt'
            )
            ->first();

        if (!$user) {
            return response()->json([
                'statusCode' => 404,
                'status'     => false,
                'message'    => 'Pengguna tidak ditemukan.',
                'data'       => null,
            ]);
        }

        $data = [
            'user_id'         => $user->user_id,
            'name'            => $user->name,
            'photo'           => $user->photo,
            'year_generation' => $user->year_generation,
            'major'           => $user->major,
            'job'             => $user->job,
            'location'        => $user->location,
            'lat'             => $user->lat,
            'lng'             => $user->lng,
            'created_dt'      => $user->created_dt,
        ];

        if ($user->user_id !== $authUser->user_id) {
            $distance = $this->calculateDistance(
                $authUser->lat,
                $authUser->lng,
                $user->lat,
                $user->lng
            );

            $friendStatus = Friend::where(function ($query) use ($authUser, $user) {
                $query->where('user_id_1', $authUser->user_id)
                    ->where('user_id_2', $user->user_id);
            })
                ->orWhere(function ($query) use ($authUser, $user) {
                    $query->where('user_id_1', $user->user_id)
                        ->where('user_id_2', $authUser->user_id);
                })
                ->first();

            $data['friend_status'] = $friendStatus ? ($friendStatus->status == 'accepted' ? true : false) : false;


            $data['distance_km'] = $distance;

            if ($distance < 1) {
                $meters = round($distance * 1000);
                $data['distance_info'] = "± {$meters} m";
            } else {
                $formatted = number_format($distance, 2);
                $data['distance_info'] = "± {$formatted} km";
            }
        }

        return response()->json([
            'statusCode' => 200,
            'status'     => true,
            'message'    => 'Detail data berhasil dimuat.',
            'data'       => $data,
        ]);
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo   = deg2rad($lat2);
        $lngTo   = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return round($earthRadius * $angle, 2);
    }
}
