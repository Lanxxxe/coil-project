<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacesPhoto extends Model
{
    protected $table = 'places_photo';

    protected $fillable = [
        'place_id',
        'caption',
        'description',
        'url',
        'filename'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Relationship with Places model
    public function place()
    {
        return $this->belongsTo(Places::class, 'place_id', 'place_id');
    }

    // Get photos by place ID
    public static function getByPlaceId(int $placeId)
    {
        return self::where('place_id', $placeId)->get();
    }

   
    // Get specific photo with its place
    public static function getWithPlace(int $photoId): ?PlacesPhoto
    {
        return self::with('place')->find($photoId);
    }

    // Get all photos with their places
    public static function getAllWithPlaces()
    {
        return self::with('place')->get();
    }
}
