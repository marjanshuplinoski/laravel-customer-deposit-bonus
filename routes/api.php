<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
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
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('editCustomer/{id}', [CustomerController::class, 'editCustomer']);
Route::post('deposit/{id}/{sum}', [CustomerController::class, 'deposit']);
Route::post('withdraw/{id}/{sum}', [CustomerController::class, 'withdraw']);
Route::post('report', [CustomerController::class, 'report']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
