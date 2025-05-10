<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::middleware(['firebase.auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return view('panel');
    });
    Route::get('orders', function () {
        return view('orders');
    });

    Route::get('products' , [ProductsController::class , 'index']);
    Route::get('users', [UsersController::class, 'index']);

    Route::get('reports', function () {
        return view('reports');
    });
});

Route::get('/login', [AuthController::class, 'index'])
    ->middleware('firebase.guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('firebase.auth')
    ->name('logout');
