<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Frontend pages (Livewire)
Route::view('/food', 'pages.food')->name('food.index');
Route::view('/places', 'pages.places')->name('places.index');
