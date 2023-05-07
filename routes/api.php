<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer\HomeController as BuyerHome;
use App\Http\Controllers\Posts\HomeController as PostHome;
use App\Http\Controllers\Seller\HomeController as SellerHome;
use App\Http\Controllers\Cashier\HomeController as CashierHome;
use App\Http\Controllers\Users\HomeController as UsersHome;
use App\Http\Controllers\Posts\Comments\HomeController as CommentHome;
use App\Http\Controllers\Posts\Like\HomeController as LikeHome;
use App\Http\Controllers\Message\HomeController as MessageHome;
use App\Http\Controllers\ChangeAccount\HomeController as ChangeHome;
use App\Http\Controllers\Friend\HomeController as FriendHome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('ChangeUser', [ChangeHome::class, 'changeUser'])->middleware(['auth:api']);
Route::post('login', [AuthController::class, 'LoginUser']);
Route::post('register', [AuthController::class, 'RegisterUser']);
Route::get('logout', [AuthController::class, 'LogoutUser'])->middleware(['auth:api']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('authenticate', [AuthController::class, 'authenticate']);
Route::post('login', [AuthController::class, 'LoginUser']);
Route::post('register', [AuthController::class, 'RegisterUser']);
Route::get('logout', [AuthController::class, 'LogoutUser'])->middleware(['auth:api']);

// Buyer
Route::prefix('buyer')->group(function () {
    Route::get('{user}', [BuyerHome::class, 'home']);
    Route::post('pay', [BuyerHome::class, 'store']);
});

// Seller / Merchant
Route::prefix('seller')->group(function () {
    Route::get('{user}', [SellerHome::class, 'home']);
});

// Cashier
Route::prefix('cashier')->group(function () {
    Route::get('{user}', [CashierHome::class, 'home']);
    Route::post('topup', [CashierHome::class, 'store']);
});

// users
Route::prefix('user')->group(function () {
    Route::get('/', [UsersHome::class, 'home']);
    Route::get('list-user', [UsersHome::class, 'listUser']);
    Route::get('searchUser/{username}', [UsersHome::class, 'SearchUserbyName']);
    Route::get('UserProfile/{username}', [UsersHome::class, 'userProfile']);
    Route::patch('edit-user', [UsersHome::class, 'updateUser']);
    Route::post('delete-user/{user}', [UsersHome::class, 'deleteUser']);
});
Route::prefix('post')->group(function () {
    // add posts
    Route::post('add-posts', [PostHome::class, 'storePosts'])->middleware(['auth:api']);
    // get posts
    Route::get('get-posts', [PostHome::class, 'getPostByUser'])->middleware(['auth:api']);
    // get all posts
    Route::get('getallposts', [PostHome::class, 'getAllPosts']);
    Route::prefix('comments')->group(function () {
        // add comments
        Route::post('add-comment/{postid}', [CommentHome::class, 'storeComment'])->middleware(['auth:api']);
        // get comments
        Route::get('get-comment/{userid}', [CommentHome::class, 'getCommentByUser']);
        //get all comment
        Route::get('getallcomment', [CommentHome::class, 'GetAllComments']);

    });
    Route::prefix('like')->group(function () {
        // add like
        Route::post('add-like/{postid}', [LikeHome::class, 'LikePost']);
    });
    
});
//message
Route::prefix('message')->group(function () {
    Route::get('get-message/{senderid}', [MessageHome::class, 'getMessage'])->middleware(['auth:api']);
    Route::post('add-message', [MessageHome::class, 'SendMessage'])->middleware(['auth:api']);
});
//follow and followers
Route::prefix('follow')->group(function () {
    Route::get('show-followers', [FriendHome::class, 'showFriends'])->middleware(['auth:api']);
    Route::post('follow', [FriendHome::class, 'Follow'])->middleware(['auth:api']);
    Route::post('follow-request', [FriendHome::class, 'RespondRequest'])->middleware(['auth:api']);
});
