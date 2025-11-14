<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Auth\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Banner\BannerController;
use App\Http\Controllers\BannerTenant\BannerTenantController;
use App\Http\Controllers\Chattings\ChatController;
use App\Http\Controllers\CheckVersion\AppVersionController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\ForumCommunity\CommunityCategoryController;
use App\Http\Controllers\ForumCommunity\CommunityController;
use App\Http\Controllers\ForumComunity\DiscussionCommentController;
use App\Http\Controllers\ForumComunity\DiscussionController;
use App\Http\Controllers\ForumComunity\DiscussionLikeController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\Master\SystemMasterController;
use App\Http\Controllers\MasterData\Academic\DepartmentController;
use App\Http\Controllers\MasterData\Academic\ProgramController;
use App\Http\Controllers\Tenant\ShoppingbyVoucherController;
use App\Http\Controllers\Tenant\TenantCategoryController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\User\FriendController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ShareLocationController;
use App\Http\Controllers\User\UserListController;
use App\Http\Controllers\Wall\PostController;

Route::get('/check-version', [AppVersionController::class, 'check']);

Route::prefix('auth')->group(function () {
    Route::post('/register/step1', [RegisterController::class, 'registerStep1']);
    Route::post('/register/step2', [RegisterController::class, 'registerStep2']);
    Route::post('/activate', [RegisterController::class, 'activate']);
    Route::post('/resend-otp', [RegisterController::class, 'resendOtp']);
    Route::post('/login', [LoginController::class, 'login']);
});

Route::prefix('account')->group(function () {
    // Lupa Password
    Route::post('/reset/send-otp', [PasswordResetController::class, 'sendOtpPasswordReset']);
    Route::post('/reset/verify', [PasswordResetController::class, 'verifyOtpPasswordReset']);
    Route::post('/reset-password', [PasswordResetController::class, 'setNewPassword']);
});

Route::prefix('master-data')->group(function () {
    // Departement
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{department_id}', [DepartmentController::class, 'show']);

    // Programs
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{program_id}', [ProgramController::class, 'show']);

    Route::get('/locations', [LocationController::class, 'index']);
});


Route::middleware(['auth:api'])->group(function () {

    Route::prefix('system-master')->group(function () {
        Route::get('/', [SystemMasterController::class, 'index']);       // Ambil semua atau berdasarkan filter
        Route::get('/{id}', [SystemMasterController::class, 'show']);    // Ambil berdasarkan ID
        Route::put('/{id}', [SystemMasterController::class, 'update']);  // Update value-nya
    });

    // Banner 
    Route::prefix('banner')->group(function () {
        Route::get('/spotlight', [BannerController::class, 'index']);
        Route::get('/spotlight/banner_id', [BannerController::class, 'show']);
        Route::get('/banner-tenant', [BannerTenantController::class, 'index']);
    });

    // Departement
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{department_id}', [DepartmentController::class, 'show']);

    // Programs
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{program_id}', [ProgramController::class, 'show']);

    // Profil pengguna
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::put('/profile-experience', [ProfileController::class, 'updateExperiences']);
    Route::delete('/profile-experience/{experience_id}', [ProfileController::class, 'deleteExperienceById']);

    // Share Location Pengguna
    Route::patch('/profile/share-location', [ShareLocationController::class, 'updateShareLocation']);

    // Ubah Password
    Route::post('/change/send-otp', [AccountController::class, 'sendOtpChangePassword']);
    Route::post('/change/verify-otp', [AccountController::class, 'verifyOtpChangePassword']);
    Route::post('/change/password', [AccountController::class, 'changePassword']);

    // Artikel
    Route::get('/articles', [ArticleController::class, 'getAllArticles']);
    Route::get('/articles/{article_id}', [ArticleController::class, 'getArticleDetail']);

    // Acara
    Route::get('/events', [EventController::class, 'getAllEvents']);
    Route::get('/events/{event_id}', [EventController::class, 'getEventDetail']);

    // Users & Friend list
    Route::get('/users', [UserListController::class, 'index']);
    Route::get('/users-recommendations', [UserListController::class, 'getFriendRecommendations']);
    Route::get('/users/favorites', [UserListController::class, 'getFavorites']);
    Route::get('/users/{user_id}', [UserListController::class, 'show']);

    // Permintaan pertemanan (Friend Requests)
    Route::post('/friend/favorite', [FriendController::class, 'addFavorite']);
    Route::delete('/friend/favorite-remove', [FriendController::class, 'removeFavorite']);
    Route::post('/friend/send-request', [FriendController::class, 'sendRequest']);
    Route::post('/friend/accept-request', [FriendController::class, 'acceptRequest']);
    Route::get('/friend/get-friends', [FriendController::class, 'getFriends']);
    Route::get('/friend/get-incoming-requests', [FriendController::class, 'getIncomingRequests']);
    Route::get('/friend/get-outgoing-requests', [FriendController::class, 'getOutgoingRequests']);

    // Tenant Category routes
    Route::get('/tenant-categories', [TenantCategoryController::class, 'index']);

    // Tenants
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenant/{tenant_id}', [TenantController::class, 'show']);

    // ShoppingbyVocher
    Route::get('/promo-categories', [ShoppingbyVoucherController::class, 'listVoucherCategories']);
    Route::get('/grouped-promos', [ShoppingbyVoucherController::class, 'listVouchersWithTenant']);
    Route::get('/grouped-promos/{voucher_id}', [ShoppingbyVoucherController::class, 'getVoucherDetail']);

    //Community Categories
    Route::get('/community-categories', [CommunityCategoryController::class, 'index']);
    Route::post('/community', [CommunityCategoryController::class, 'store']);
    Route::delete('/community/{communityId}/members/{memberId}', [CommunityCategoryController::class, 'kickMember']);


    //Forum Cominity
    Route::get('/communities', [CommunityController::class, 'index']);
    Route::get('/my-communities', [CommunityController::class, 'myCommunities']);
    Route::get('/communities/{community_id}', [CommunityController::class, 'show']);
    Route::post('/communities/join/{community_id}', [CommunityController::class, 'join']);
    Route::delete('/communities/unjoin/{community_id}', [CommunityController::class, 'unjoin']);

    // Discussion Routes
    Route::get('/discussions', [DiscussionController::class, 'all']);
    Route::get('/discussions/{discus_id}', [DiscussionController::class, 'show']);
    Route::delete('/discussions/discus/role/{discus_id}', [DiscussionController::class, 'deleteDiscussion']);
    Route::delete('/discussions/{discus_id}', [DiscussionController::class, 'destroy']);
    Route::get('/community/discussions/{community_id}', [DiscussionController::class, 'index']);
    Route::post('/community/discussion/{community_id}', [DiscussionController::class, 'store']);

    // Discussion Comment Routes
    Route::get('/discussion/comments/{discus_id}', [DiscussionCommentController::class, 'index']);
    Route::post('/discussion/comments/{discus_id}', [DiscussionCommentController::class, 'store']);
    Route::post('comment/like/{comment_id}', [DiscussionCommentController::class, 'likeComment']);
    Route::post('comment/unlike/{comment_id}', [DiscussionCommentController::class, 'unlikeComment']);

    // Discussion Like Routes
    Route::post('/like/discussion/{discus_id}', [DiscussionLikeController::class, 'store']);
    Route::delete('/like/discussion/{discus_id}', [DiscussionLikeController::class, 'destroy']);

    // Chattings
    Route::post('chat/room', [ChatController::class, 'createRoom']);
    Route::get('chat/get-message/{chroom_id}', [ChatController::class, 'getMessages']);
    Route::post('chat/send-message/{chroom_id}', [ChatController::class, 'sendMessage']);

    // Wall Post
    Route::get('/wall/{user_id}', [PostController::class, 'showWall']);
    Route::post('/wall/create-post', [PostController::class, 'createPost']);
    Route::get('/post/{post_id}', [PostController::class, 'showPostDetail']);
    Route::delete('/post/{post_id}', [PostController::class, 'deletePost']);
    Route::post('/post/comment/{post_id}', [PostController::class, 'createComment']);
    Route::get('/post/comment/{post_id}', [PostController::class, 'getCommentsByPost']);
    Route::post('/post/like/{post_id}', [PostController::class, 'likePost']);
    Route::get('/posts/{post_id}/likes', [PostController::class, 'getLikesByPost']);
});
