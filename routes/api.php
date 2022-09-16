<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/registration', [RegistrationController::class, 'Registration']);
Route::post('/login', [LoginController::class, 'Login']);
Route::post('/logout', [LoginController::class, 'Logout']);
Route::post('/deleteUser', [RegistrationController::class, 'deleteUser']);

Route::post('/addProduct', [ProductController::class, 'AddProduct']);
Route::get('/allProducts', [ProductController::class, 'ProductList']);
Route::get('/featuredImages/{id}', [ProductController::class, 'FeaturedImages']);
Route::get('/productDetails/{id}', [ProductController::class, 'ProductDetails']);
Route::delete('/deleteProduct/{id}', [ProductController::class, 'deleteProduct']);