<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseController;
Route::get('/', function () {
    return view('panel');
});
Route::get('orders', function () {
    return view('orders');
});

Route::get('products', function () {
    return view('products');
});
Route::get('users', [FirebaseController::class, 'index']);
Route::get('reports', function () {
    return view('reports');
});

