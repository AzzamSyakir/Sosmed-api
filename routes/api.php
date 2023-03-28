<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer\HomeController as BuyerHome;
use App\Http\Controllers\Posts\HomeController as PostHome;
use App\Http\Controllers\Seller\HomeController as SellerHome;
use App\Http\Controllers\Cashier\HomeController as CashierHome;
use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Posts\Comments\HomeController as CommentHome;
use App\Http\Controllers\Posts\Like\HomeController as LikeHome;
use App\Http\Controllers\Message\HomeController as MessageHome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Admin
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminHome::class, 'home']);
    Route::get('list-user', [AdminHome::class, 'listUser'])->middleware(['auth:api']);
    Route::post('add-user', [AdminHome::class, 'storeUser']);
    Route::patch('edit-user', [AdminHome::class, 'updateUser']);
    Route::post('delete-user/{user}', [AdminHome::class, 'deleteUser']);
    Route::get('list-transaction', [AdminHome::class, 'listTransaction']);
    Route::get('list-topup', [AdminHome::class, 'listTopup']);
    Route::post('add-topup', [AdminHome::class, 'storeTopup']);
    Route::get('list-withdraw', [AdminHome::class, 'listWithdraw']);
    Route::post('add-withdraw', [AdminHome::class, 'storeWithdraw']);
});
Route::prefix('post')->group(function () {
    // add posts
    Route::post('add-posts', [PostHome::class, 'storePosts'])->middleware(['auth:api']);
    // get posts
    Route::get('get-posts', [PostHome::class, 'getPostByUser'])->middleware(['auth:api']);
    // get all posts
    Route::get('getall-posts', [PostHome::class, 'getAllPosts']);
    Route::prefix('comments')->group(function () {
        // add comments
        Route::post('add-comment/{postid}', [CommentHome::class, 'storeComment']);
        // get comments
        Route::get('get-comment', [CommentHome::class, 'getCommentByUser']);
        //get all comment
        Route::get('getall-comment', [CommentHome::class, 'GetAllComments']);

    });
    Route::prefix('like')->group(function () {
        // add like
        Route::post('add-like/{postid}', [LikeHome::class, 'LikePost']);
    });
});
