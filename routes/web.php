<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Frontend pages
use App\Http\Controllers\FoodPageController;
use App\Http\Controllers\PlacesPageController;
use App\Http\Controllers\RegionPageController;

Route::get('/food', [FoodPageController::class, 'index'])->name('food.index');
Route::get('/food/{slug}', [FoodPageController::class, 'show'])->name('food.show');
Route::get('/places', [PlacesPageController::class, 'index'])->name('places.index');
Route::get('/places/{slug}', [PlacesPageController::class, 'show'])->name('places.show');

// Region detail pages
Route::get('/regions/{country}/{region}', [RegionPageController::class, 'show'])->name('regions.show');
