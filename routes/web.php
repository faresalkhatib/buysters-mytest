<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CaptainController;
use App\Http\Controllers\TransactionsController;

Route::middleware(['firebase.auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('orders', [OrdersController::class,'index'])->name('order');

    Route::get('transactions', [TransactionsController::class, 'index'])->name('transactions');
    Route::delete('transactions/{id}', [TransactionsController::class, 'destroy'])->name('transactions.destroy');
    // Product routes
    Route::get('products', [ProductsController::class, 'index'])->name('product');
    Route::delete('products/{id}', [ProductsController::class, 'destroy'])->name('product.destroy');
    Route::post('products/{id}/restore', [ProductsController::class, 'restore'])->name('product.restore');

    Route::get('users', [UsersController::class, 'index'])->name('user');

    Route::get('categories', [CategoriesController::class, 'index'])->name('category');
    Route::get('categories/create', [CategoriesController::class, 'create'])->name('category.create');
    Route::post('categories', [CategoriesController::class, 'store'])->name('category.store');
    Route::get('categories/{id}', [CategoriesController::class, 'show'])->name('category.show');
    Route::get('categories/{id}/edit', [CategoriesController::class, 'edit'])->name('category.edit');
    Route::put('categories/{id}', [CategoriesController::class, 'update'])->name('category.update');
    Route::delete('categories/{id}', [CategoriesController::class, 'destroy'])->name('category.destroy');

    Route::post('/users/{userId}/toggle-block', [UsersController::class, 'toggleBlock'])->name('users.toggle-block');

    // Captain Routes
    Route::prefix('captains')->group(function () {
        Route::get('/', [CaptainController::class, 'index'])->name('captains.index');
        Route::get('/create', [CaptainController::class, 'create'])->name('captains.create');
        Route::post('/', [CaptainController::class, 'store'])->name('captains.store');
        Route::get('/{id}', [CaptainController::class, 'show'])->name('captains.show');
        Route::patch('/{id}/toggle-status', [CaptainController::class, 'toggleStatus'])->name('captains.toggleStatus');
    });

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
