<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Places extends Model
{

    protected $table = 'places';
    protected $primaryKey = 'place_id';
    protected $fillable = [
        'name',
    'slug',
        'description',
        'latitude',
        'longitude',
        'country',
        'type',
        'location',
        'caption'
    ];
    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getPlaces()
    {
        return $this->all();
    }

    // Relationship with PlacesPhoto model
    public function photos()
    {
        return $this->hasMany(PlacesPhoto::class, 'place_id', 'place_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Places $place) {
            if (!$place->slug && $place->name) {
                $base = \Illuminate\Support\Str::slug($place->name);
                $place->slug = $base ?: null;
            }
        });
    }

    
    // Get specific place using the name of the place (with photos)
    public static function getByNameWithPhotos(string $name): ?Places
    {
        return self::with('photos')->where('name', $name)->first();
    }

    
    // Get specific place using partial name match (with photos)
    public static function getByNameLikeWithPhotos(string $name): ?Places
    {
        return self::with('photos')->where('name', 'LIKE', '%' . $name . '%')->first();
    }

    public static function getBySlugWithPhotos(string $slug): ?Places
    {
        return self::with('photos')->where('slug', $slug)->first();
    }

    
    // Get all place data (with photos)
    public static function getAllPlacesWithPhotos(): Collection
    {
        return self::with('photos')->get();
    }


    // Get all places with ordering options (with photos)
    public static function getAllPlacesOrderedWithPhotos(string $orderBy = 'name', string $direction = 'asc'): Collection
    {
        return self::with('photos')->orderBy($orderBy, $direction)->get();
    }


    // Get n number of place data (with photos)
    public static function getNPlacesWithPhotos(int $limit): Collection
    {
        return self::with('photos')->limit($limit)->get();
    }


    // Get n number of place data with ordering (with photos)
    public static function getNPlacesOrderedWithPhotos(int $limit, string $orderBy = 'name', string $direction = 'asc'): Collection
    {
        return self::with('photos')->orderBy($orderBy, $direction)->limit($limit)->get();
    }


    // Search places by name, description, location, or caption (with photos)
    public static function searchWithPhotos(string $searchTerm): Collection
    {
        return self::with('photos')->where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('location', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('caption', 'LIKE', '%' . $searchTerm . '%');
        })->get();
    }


    // Get a set of places by IDs (with photos)
    public static function getSetOfPlacesWithPhotos(array $placeIds): Collection
    {
        return self::with('photos')->whereIn('place_id', $placeIds)->get();
    }


    // Get places by type (with photos)
    public static function getByTypeWithPhotos(string $type): Collection
    {
        return self::with('photos')->where('type', $type)->get();
    }


    // Get places by country (with photos)
    public static function getByCountryWithPhotos(string $country): Collection
    {
        return self::with('photos')->where('country', $country)->get();
    }


    // Get places within coordinate bounds (with photos)
    public static function getPlacesInBoundsWithPhotos(float $minLat, float $maxLat, float $minLng, float $maxLng): Collection
    {
        return self::with('photos')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->get();
    }


    // Get random places (with photos)
    public static function getRandomWithPhotos(int $count = 1): Collection
    {
        return self::with('photos')->inRandomOrder()->limit($count)->get();
    }


    // Get paginated places data (with photos)
    public static function getPaginatedWithPhotos(int $perPage = 10)
    {
        return self::with('photos')->paginate($perPage);
    }


    // Get all unique types
    public static function getTypes(): SupportCollection
    {
        return self::distinct()->pluck('type')->filter();
    }


    // Get all unique countries
    public static function getCountries(): SupportCollection
    {
        return self::distinct()->pluck('country')->filter();
    }


    // Advanced search with multiple filters (with photos)
    public static function advancedSearchWithPhotos(array $filters): Collection
    {
        $query = self::with('photos');

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (isset($filters['search_term'])) {
            $query->where(function ($subQuery) use ($filters) {
                $subQuery->where('name', 'LIKE', '%' . $filters['search_term'] . '%')
                         ->orWhere('description', 'LIKE', '%' . $filters['search_term'] . '%')
                         ->orWhere('location', 'LIKE', '%' . $filters['search_term'] . '%');
            });
        }

        if (isset($filters['order_by'])) {
            $direction = $filters['order_direction'] ?? 'asc';
            $query->orderBy($filters['order_by'], $direction);
        }

        if (isset($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->get();
    }
}
