<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer\HomeController as BuyerHome;
use App\Http\Controllers\Posts\HomeController as PostHome;
use App\Http\Controllers\Seller\HomeController as SellerHome;
use App\Http\Controllers\Cahsier\HomeController as CashierHome;
use App\Http\Controllers\Admin\HomeController as AdminHome;
use App\Http\Controllers\Posts\Comments\HomeController as CommentHome;
use App\Http\Controllers\Posts\Like\HomeController as LikeHome;
use App\Http\Controllers\Message\HomeController as MessageHome;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
php 
// Auth 
Route::post('authenticate', [AuthController::class, 'authenticate']);
Route::post('login', [AuthController::class, 'LoginUser']);
Route::post('register', [AuthController::class, 'RegisterUser']);
Route::get('logout', [AuthController::class, 'LogoutUser'])->middleware(['auth:api']);


// Buyer
Route::prefix('buyer')->controller(BuyerHome::class)->group(function () {
    Route::get('{user}', 'home');
    Route::post('pay', 'store');
});


// Seller / Merchant
Route::prefix('seller')->controller(SellerHome::class)->group(function () {
    Route::get('{user}', 'home');
});


// Cashier
Route::prefix('cashier')->controller(CashierHome::class)->group(function () {
    Route::get('{user}', 'home');
    Route::post('topup', 'store');
});


// Admin
Route::prefix('admin')->controller(AdminHome::class)->group(function () {

    // User
    Route::get('/', 'home');
    Route::get('list-user', 'listUser')->middleware(['auth:api']);
    Route::post('add-user', 'StoreUser');
    Route::patch('edit-user', 'updateUser');
    Route::post('delete-user/{user}', 'DeleteUser');


    // Transaksi
    Route::get('list-transaction', 'listTransaction');


    // Topup
    Route::get('list-topup', 'listTopup');
    Route::post('add-topup', 'storeTopup');


    // Withdraw
    Route::get('list-withdraw', 'listWithdraw');
    Route::post('add-withdraw', 'storeWithdraw');
});

Route::prefix('post')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        //add posts
        Route::post('add-posts', [PostHome::class, 'StorePosts']);
        //getposts
        Route::get('get-posts', [PostHome::class, 'GetPostbyUser']);

        Route::prefix('comments')->group(function () {
            //add comments
            Route::post('add-comment/{postid}', [CommentHome::class, 'StoreComment']);
            //get comment
            Route::get('get-comment', [CommentHome::class, 'GetCommentbyUser']);
        });

        Route::prefix('like')->group(function () {
            //add like
            Route::post('add-like/{postid}', [LikeHome::class, 'LikePost']);
            //get like
            Route::get('get-like', [LikeHome::class, 'GetLikebyUser']);
        });
    });
});



//chat fitur
Route::prefix('message')->controller(MessageHome::class)->group(function () {
    //add comments
    Route::post('add-message', 'SendMessage')->middleware(['auth:api']);
    //get comment
    Route::get('get-message/{senderid}', 'getMessage')->middleware(['auth:api']);

});
