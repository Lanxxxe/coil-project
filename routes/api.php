<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PlacesController;
use App\Http\Controllers\GeoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Food API Routes
Route::prefix('food')->group(function () {
    // Get all food items
    Route::get('/', [FoodController::class, 'index']);
    
    // Get specific food by name
    Route::get('/show/{name}', [FoodController::class, 'show']);
    
    // Get limited number of food items
    Route::get('/limited', [FoodController::class, 'getLimited']);
    
    // Search food items
    Route::get('/search', [FoodController::class, 'search']);
    
    // Get food by category
    Route::get('/category/{category}', [FoodController::class, 'getByCategory']);
    
    // Get food by place of origin
    Route::get('/place/{place}', [FoodController::class, 'getByPlace']);
    
    // Get food by price range
    Route::get('/price-range', [FoodController::class, 'getByPriceRange']);
    
    // Get random food items
    Route::get('/random', [FoodController::class, 'getRandom']);
    
    // Get paginated food items
    Route::get('/paginated', [FoodController::class, 'getPaginated']);
    
    // Get all categories
    Route::get('/categories', [FoodController::class, 'getCategories']);
    
    // Get all places of origin
    Route::get('/places-of-origin', [FoodController::class, 'getPlacesOfOrigin']);
    
    // Advanced search
    Route::post('/advanced-search', [FoodController::class, 'advancedSearch']);
});

// Places API Routes
Route::prefix('places')->group(function () {
    // Get all places with photos
    Route::get('/', [PlacesController::class, 'index']);
    
    // Get specific place by name with photos
    Route::get('/show/{name}', [PlacesController::class, 'show']);
    
    // Get specific place by ID with photos
    Route::get('/id/{id}', [PlacesController::class, 'getById']);
    
    // Get limited number of places with photos
    Route::get('/limited', [PlacesController::class, 'getLimited']);
    
    // Search places with photos
    Route::get('/search', [PlacesController::class, 'search']);
    
    // Get places by type with photos
    Route::get('/type/{type}', [PlacesController::class, 'getByType']);
    
    // Get places by country with photos
    Route::get('/country/{country}', [PlacesController::class, 'getByCountry']);
    
    // Get places within coordinate bounds with photos
    Route::get('/bounds', [PlacesController::class, 'getInBounds']);
    
    // Get set of places by IDs with photos
    Route::post('/set', [PlacesController::class, 'getSetOfPlaces']);
    
    // Get random places with photos
    Route::get('/random', [PlacesController::class, 'getRandom']);
    
    // Get paginated places with photos
    Route::get('/paginated', [PlacesController::class, 'getPaginated']);
    
    // Get all place types
    Route::get('/types', [PlacesController::class, 'getTypes']);
    
    // Get all countries
    Route::get('/countries', [PlacesController::class, 'getCountries']);
    
    // Advanced search with photos
    Route::post('/advanced-search', [PlacesController::class, 'advancedSearch']);

    // Get top places by country and region
    Route::get('/by-region', [PlacesController::class, 'getByCountryRegion']);
});

// Geo boundaries proxy (same-origin)
Route::get('/geo/adm1', [GeoController::class, 'adm1']);