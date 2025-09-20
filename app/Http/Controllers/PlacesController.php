<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Places;
use Illuminate\Validation\ValidationException;

class PlacesController extends Controller
{
    /**
     * Get all places with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->get('order_by', 'name');
            $direction = $request->get('direction', 'asc');
            
            // Validate ordering parameters
            $validColumns = ['name', 'type', 'country', 'location', 'created_at'];
            $validDirections = ['asc', 'desc'];
            
            if (!in_array($orderBy, $validColumns)) {
                $orderBy = 'name';
            }
            
            if (!in_array($direction, $validDirections)) {
                $direction = 'asc';
            }
            
            $places = Places::getAllPlacesOrderedWithPhotos($orderBy, $direction);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Places with photos retrieved successfully',
                'data' => $places,
                'count' => $places->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve places',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific place by name with photos
     * 
     * @param string $name
     * @return JsonResponse
     */
    public function show(string $name): JsonResponse
    {
        try {
            $place = Places::getByNameWithPhotos($name);
            
            if (!$place) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Place not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Place with photos retrieved successfully',
                'data' => $place
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve place',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get limited number of places with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getLimited(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'required|integer|min:1|max:100',
                'order_by' => 'string|in:name,type,country,location,created_at',
                'direction' => 'string|in:asc,desc'
            ]);
            
            $limit = $request->get('limit');
            $orderBy = $request->get('order_by', 'name');
            $direction = $request->get('direction', 'asc');
            
            $places = Places::getNPlacesOrderedWithPhotos($limit, $orderBy, $direction);
            
            return response()->json([
                'status' => 'success',
                'message' => "Top {$limit} places with photos retrieved successfully",
                'data' => $places,
                'count' => $places->count(),
                'limit' => $limit
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve limited places',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search places with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1|max:255'
            ]);
            
            $searchTerm = $request->get('query');
            $places = Places::searchWithPhotos($searchTerm);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Search completed successfully',
                'data' => $places,
                'count' => $places->count(),
                'search_term' => $searchTerm
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get places by type with photos
     * 
     * @param string $type
     * @return JsonResponse
     */
    public function getByType(string $type): JsonResponse
    {
        try {
            $places = Places::getByTypeWithPhotos($type);
            
            return response()->json([
                'status' => 'success',
                'message' => "Places of type '{$type}' with photos retrieved successfully",
                'data' => $places,
                'count' => $places->count(),
                'type' => $type
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve places by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get places by country with photos
     * 
     * @param string $country
     * @return JsonResponse
     */
    public function getByCountry(string $country): JsonResponse
    {
        try {
            $places = Places::getByCountryWithPhotos($country);
            
            return response()->json([
                'status' => 'success',
                'message' => "Places in '{$country}' with photos retrieved successfully",
                'data' => $places,
                'count' => $places->count(),
                'country' => $country
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve places by country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get places within coordinate bounds with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getInBounds(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'min_lat' => 'required|numeric|between:-90,90',
                'max_lat' => 'required|numeric|between:-90,90|gte:min_lat',
                'min_lng' => 'required|numeric|between:-180,180',
                'max_lng' => 'required|numeric|between:-180,180|gte:min_lng'
            ]);
            
            $minLat = $request->get('min_lat');
            $maxLat = $request->get('max_lat');
            $minLng = $request->get('min_lng');
            $maxLng = $request->get('max_lng');
            
            $places = Places::getPlacesInBoundsWithPhotos($minLat, $maxLat, $minLng, $maxLng);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Places within coordinate bounds with photos retrieved successfully',
                'data' => $places,
                'count' => $places->count(),
                'bounds' => [
                    'min_lat' => $minLat,
                    'max_lat' => $maxLat,
                    'min_lng' => $minLng,
                    'max_lng' => $maxLng
                ]
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve places within bounds',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get set of places by IDs with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getSetOfPlaces(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'place_ids' => 'required|array|min:1|max:50',
                'place_ids.*' => 'integer|exists:places,place_id'
            ]);
            
            $placeIds = $request->get('place_ids');
            $places = Places::getSetOfPlacesWithPhotos($placeIds);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Set of places with photos retrieved successfully',
                'data' => $places,
                'count' => $places->count(),
                'requested_ids' => $placeIds
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve set of places',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get random places with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getRandom(Request $request): JsonResponse
    {
        try {
            $count = $request->get('count', 1);
            
            // Validate count parameter
            if ($count < 1 || $count > 20) {
                $count = 1;
            }
            
            $places = Places::getRandomWithPhotos($count);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Random places with photos retrieved successfully',
                'data' => $places,
                'count' => $places->count(),
                'requested_count' => $count
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve random places',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated places with photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPaginated(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            
            // Validate per_page parameter
            if ($perPage < 1 || $perPage > 50) {
                $perPage = 10;
            }
            
            $places = Places::getPaginatedWithPhotos($perPage);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Paginated places with photos retrieved successfully',
                'data' => $places->items(),
                'pagination' => [
                    'current_page' => $places->currentPage(),
                    'last_page' => $places->lastPage(),
                    'per_page' => $places->perPage(),
                    'total' => $places->total(),
                    'from' => $places->firstItem(),
                    'to' => $places->lastItem(),
                    'has_more_pages' => $places->hasMorePages()
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve paginated places',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all unique place types
     * 
     * @return JsonResponse
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = Places::getTypes();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Place types retrieved successfully',
                'data' => $types,
                'count' => $types->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve place types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all unique countries
     * 
     * @return JsonResponse
     */
    public function getCountries(): JsonResponse
    {
        try {
            $countries = Places::getCountries();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Countries retrieved successfully',
                'data' => $countries,
                'count' => $countries->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advanced search with multiple filters and photos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'type' => 'nullable|string|in:landmark,restaurant,heritage,gallery,museum,other',
                'country' => 'nullable|string|max:100',
                'search_term' => 'nullable|string|max:255',
                'order_by' => 'nullable|string|in:name,type,country,location,created_at',
                'order_direction' => 'nullable|string|in:asc,desc',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);
            
            $filters = $request->only([
                'name', 'type', 'country', 'search_term', 
                'order_by', 'order_direction', 'limit'
            ]);
            
            $places = Places::advancedSearchWithPhotos($filters);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Advanced search completed successfully',
                'data' => $places,
                'count' => $places->count(),
                'filters' => $filters
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Advanced search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get place by ID with photos
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getById(int $id): JsonResponse
    {
        try {
            $place = Places::with('photos')->find($id);
            
            if (!$place) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Place not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Place with photos retrieved successfully',
                'data' => $place
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve place',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
