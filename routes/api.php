<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// مسارات التسجيل وتسجيل الدخول
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

// مسارات محمية للمستخدمين المسجلين
Route::middleware('auth:api')->group(function () {
    Route::get('userInfo', [AuthController::class, 'userInfo']);
    
    // مسارات إدارة المنتجات محمية للمشرفين فقط
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class);
    });
});
