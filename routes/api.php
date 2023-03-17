<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer\HomeController as BuyerHome;
use App\Http\Controllers\Seller\HomeController as SellerHome;
use App\Http\Controllers\Cahsier\HomeController as CashierHome;
use App\Http\Controllers\Admin\HomeController as AdminHome;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth 
Route::post('authenticate', [AuthController::class, 'authenticate']);
Route::post('login', [AuthController::class, 'LoginUser']);
Route::get('logout', [AuthController::class, 'LogoutUser']);


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
    Route::post('add-user', 'storeUser');
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