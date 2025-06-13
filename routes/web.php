<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;

Route::middleware(['firebase.auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('orders', [OrdersController::class,'index'])->name('order');
    Route::get('products' , [ProductsController::class , 'index'])->name('product');
    Route::get('users', [UsersController::class, 'index'])->name('user');

    Route::get('categories' , [CategoriesController::class , 'index'])->name('category');
    Route::get('categories/create' , [CategoriesController::class , 'create'])->name('category.create');
    Route::post('categories' , [CategoriesController::class , 'store'])->name('category.store');
    Route::get('categories/{id}' , [CategoriesController::class , 'show'])->name('category.show');
    Route::get('categories/{id}/edit' , [CategoriesController::class , 'edit'])->name('category.edit');
    Route::put('categories/{id}' , [CategoriesController::class , 'update'])->name('category.update');
    Route::delete('categories/{id}' , [CategoriesController::class , 'destroy'])->name('category.destroy');

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
