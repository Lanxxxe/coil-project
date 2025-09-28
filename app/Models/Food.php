<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Food extends Model
{

    protected $table = 'food';


    protected $fillable = [
        'name',
        'slug',
        'description',
        'place_of_origin',
        'image_url',
        'category',
        'price',
        'caption',
        'filename'
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Food $food) {
            if (!$food->slug && $food->name) {
                $base = \Illuminate\Support\Str::slug($food->name);
                $food->slug = $base ?: null;
            }
        });
    }


    // Get specific food using the name of the food
    public static function getByName(string $name): ?Food
    {
        return self::where('name', $name)->first();
    }

    public static function getBySlug(string $slug): ?Food
    {
        return self::where('slug', $slug)->first();
    }

    
    // Get specific food using partial name match (case-insensitive)
    public static function getByNameLike(string $name): ?Food
    {
        return self::where('name', 'LIKE', '%' . $name . '%')->first();
    }


    // Get all food data
    public static function getAllFood(): Collection
    {
        return self::all();
    }


    // Get all food data with ordering options
    public static function getAllFoodOrdered(string $orderBy = 'name', string $direction = 'asc'): Collection
    {
        return self::orderBy($orderBy, $direction)->get();
    }


    // Get n number of food data
    public static function getNFood(int $limit): Collection
    {
        return self::limit($limit)->get();
    }

    
    // Get n number of food data with ordering
    public static function getNFoodOrdered(int $limit, string $orderBy = 'name', string $direction = 'asc'): Collection
    {
        return self::orderBy($orderBy, $direction)->limit($limit)->get();
    }

    
    // Get food by category
    public static function getByCategory(string $category): Collection
    {
        return self::where('category', $category)->get();
    }

    
    // Get food by place of origin
    public static function getByPlaceOfOrigin(string $placeOfOrigin): Collection
    {
        return self::where('place_of_origin', 'LIKE', '%' . $placeOfOrigin . '%')->get();
    }


    // Get food within price range
    public static function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return self::whereBetween('price', [$minPrice, $maxPrice])->get();
    }


    // Search food by name, description, or caption
    public static function search(string $searchTerm): Collection
    {
        return self::where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('caption', 'LIKE', '%' . $searchTerm . '%');
        })->get();
    }


    // Get random food items
    public static function getRandom(int $count = 1): Collection
    {
        return self::inRandomOrder()->limit($count)->get();
    }

    
    // Get paginated food data
    public static function getPaginated(int $perPage = 10)
    {
        return self::paginate($perPage);
    }

   
    // Get all unique categories
    public static function getCategories(): SupportCollection
    {
        return self::distinct()->pluck('category')->filter();
    }

    
    // Get all unique places of origin
    public static function getPlacesOfOrigin(): SupportCollection
    {
        return self::distinct()->pluck('place_of_origin')->filter();
    }
}
