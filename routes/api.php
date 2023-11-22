<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product;
use App\Http\Controllers\Api\Image;
use App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\Cart;

Route::get('/products/get_all', [Product::class, 'index']);
Route::get('/products/single', [Product::class, 'single']);
Route::post('/products/add', [Product::class, 'store']);
Route::post('/products/edit', [Product::class, 'edit']);
Route::post('/products/delete', [Product::class, 'destroy']);

//show image
Route::get('/images/{filename}', [Image::class, 'show']);

//cart
Route::get('/cart/get_cart', [Cart::class, 'getCart']);
Route::post('/cart/add_to_cart', [Cart::class, 'addToCart']);
Route::post('/cart/remove_from_cart', [Cart::class, 'removeFromCart']);

//payment simulasi dengan hapus isi cart semua
Route::post('/cart/payment', [Cart::class, 'paymentSimulation']);

//auth
Route::post('/auth/register', [Auth::class, 'register']);
Route::post('/auth/login', [Auth::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
