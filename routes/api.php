<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('tasks', TaskController::class);

    Route::middleware('admin')->group(function(){
        Route::post('/categories',[CategoryController::class, 'store']);
        Route::put('/categories/{category}',[CategoryController::class, 'update']);
        Route::delete('/categories/{category}',[CategoryController::class, 'destroy']);

        Route::post('/products',[ProductController::class, 'store']);
        Route::put('/products/{product}',[ProductController::class, 'update']);
        Route::delete('/products/{product}',[ProductController::class, 'destroy']);
    });

    Route::post('/cart/add',[CartController::class, 'addItem']);
    Route::put('/cart/items/{cartItem}',[CartController::class, 'updateQuantity']);
    Route::delete('/cart/items/{cartItem}',[CartController::class, 'removeItem']);
    Route::get('/cart',[CartController::class, 'show']);
    Route::post('/checkout',[CartController::class, 'checkout']);
    Route::get('/orders',[OrderController::class, 'index']);
    Route::get('/orders/{order}',[OrderController::class, 'show']);
});

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);

Route::get('/categories',[CategoryController::class, 'index']);
Route::get('/categories/{category}',[CategoryController::class, 'show']);

Route::get('/products',[ProductController::class, 'index']);
Route::get('/products/{product}',[ProductController::class, 'show']);

