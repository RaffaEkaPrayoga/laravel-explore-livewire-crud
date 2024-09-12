<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Products;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/produk', Products::class);