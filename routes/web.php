<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('panel');
});
Route::get('orders', function () {
    return view('orders');
});

Route::get('products', function () {
    return view('products');
});
Route::get('users', function () {
    return view('users');
});
Route::get('reports', function () {
    return view('reports');
});

