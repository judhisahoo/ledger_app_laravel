<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [UserAuthController::class,'register']);
Route::post('/login', [UserAuthController::class,'login']);

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->post('/getDetails', function (Request $request) {
    return $request->user();
});*/


Route::middleware('auth:api')->post('/get-details', [UserAuthController::class,'getDetails'])->name('get-details');
Route::middleware('auth:api')->post('/get-all-user', [UserAuthController::class,'getDetailsAll'])->name('get-all-user');
Route::middleware('auth:api')->post('/add', [UserAuthController::class,'addMoney'])->name('add');
Route::middleware('auth:api')->post('/trasnsfer', [UserAuthController::class,'trasnsfer'])->name('trasnsfer');
Route::middleware('auth:api')->post('/balance', [UserAuthController::class,'getBalance'])->name('balance');
//Route::get('/user/profile',[UserProfileController::class, 'show'])->name('profile');
