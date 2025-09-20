<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Food;
use Illuminate\Validation\ValidationException;

class FoodController extends Controller
{
    /**
     * Get all food items
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
            $validColumns = ['name', 'category', 'price', 'place_of_origin', 'created_at'];
            $validDirections = ['asc', 'desc'];
            
            if (!in_array($orderBy, $validColumns)) {
                $orderBy = 'name';
            }
            
            if (!in_array($direction, $validDirections)) {
                $direction = 'asc';
            }
            
            $foods = Food::getAllFoodOrdered($orderBy, $direction);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Food items retrieved successfully',
                'data' => $foods,
                'count' => $foods->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve food items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific food by name
     * 
     * @param string $name
     * @return JsonResponse
     */
    public function show(string $name): JsonResponse
    {
        try {
            $food = Food::getByName($name);
            
            if (!$food) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Food item not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Food item retrieved successfully',
                'data' => $food
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve food item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get limited number of food items
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getLimited(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'required|integer|min:1|max:100',
                'order_by' => 'string|in:name,category,price,place_of_origin,created_at',
                'direction' => 'string|in:asc,desc'
            ]);
            
            $limit = $request->get('limit');
            $orderBy = $request->get('order_by', 'name');
            $direction = $request->get('direction', 'asc');
            
            $foods = Food::getNFoodOrdered($limit, $orderBy, $direction);
            
            return response()->json([
                'status' => 'success',
                'message' => "Top {$limit} food items retrieved successfully",
                'data' => $foods,
                'count' => $foods->count(),
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
                'message' => 'Failed to retrieve limited food items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search food items
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
            $foods = Food::search($searchTerm);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Search completed successfully',
                'data' => $foods,
                'count' => $foods->count(),
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
     * Get food items by category
     * 
     * @param string $category
     * @return JsonResponse
     */
    public function getByCategory(string $category): JsonResponse
    {
        try {
            $foods = Food::getByCategory($category);
            
            return response()->json([
                'status' => 'success',
                'message' => "Food items in '{$category}' category retrieved successfully",
                'data' => $foods,
                'count' => $foods->count(),
                'category' => $category
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve food items by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get food items by place of origin
     * 
     * @param string $place
     * @return JsonResponse
     */
    public function getByPlace(string $place): JsonResponse
    {
        try {
            $foods = Food::getByPlaceOfOrigin($place);
            
            return response()->json([
                'status' => 'success',
                'message' => "Food items from '{$place}' retrieved successfully",
                'data' => $foods,
                'count' => $foods->count(),
                'place_of_origin' => $place
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve food items by place',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get food items within price range
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getByPriceRange(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'min_price' => 'required|numeric|min:0',
                'max_price' => 'required|numeric|min:0|gte:min_price'
            ]);
            
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            
            $foods = Food::getByPriceRange($minPrice, $maxPrice);
            
            return response()->json([
                'status' => 'success',
                'message' => "Food items within price range ₱{$minPrice} - ₱{$maxPrice} retrieved successfully",
                'data' => $foods,
                'count' => $foods->count(),
                'price_range' => [
                    'min' => $minPrice,
                    'max' => $maxPrice
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
                'message' => 'Failed to retrieve food items by price range',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get random food items
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
            
            $foods = Food::getRandom($count);
            
            return response()->json([
                'status' => 'success',
                'message' => "Random food items retrieved successfully",
                'data' => $foods,
                'count' => $foods->count(),
                'requested_count' => $count
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve random food items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated food items
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
            
            $foods = Food::getPaginated($perPage);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Paginated food items retrieved successfully',
                'data' => $foods->items(),
                'pagination' => [
                    'current_page' => $foods->currentPage(),
                    'last_page' => $foods->lastPage(),
                    'per_page' => $foods->perPage(),
                    'total' => $foods->total(),
                    'from' => $foods->firstItem(),
                    'to' => $foods->lastItem(),
                    'has_more_pages' => $foods->hasMorePages()
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve paginated food items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all unique categories
     * 
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Food::getCategories();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Food categories retrieved successfully',
                'data' => $categories,
                'count' => $categories->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve food categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all unique places of origin
     * 
     * @return JsonResponse
     */
    public function getPlacesOfOrigin(): JsonResponse
    {
        try {
            $places = Food::getPlacesOfOrigin();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Places of origin retrieved successfully',
                'data' => $places,
                'count' => $places->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve places of origin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advanced search with multiple filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:100',
                'place_of_origin' => 'nullable|string|max:255',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'order_by' => 'nullable|string|in:name,category,price,place_of_origin,created_at',
                'direction' => 'nullable|string|in:asc,desc',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);
            
            $query = Food::query();
            
            // Apply filters
            if ($request->filled('query')) {
                $searchTerm = $request->get('query');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('caption', 'LIKE', '%' . $searchTerm . '%');
                });
            }
            
            if ($request->filled('category')) {
                $query->where('category', $request->get('category'));
            }
            
            if ($request->filled('place_of_origin')) {
                $query->where('place_of_origin', 'LIKE', '%' . $request->get('place_of_origin') . '%');
            }
            
            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->whereBetween('price', [$request->get('min_price'), $request->get('max_price')]);
            } elseif ($request->filled('min_price')) {
                $query->where('price', '>=', $request->get('min_price'));
            } elseif ($request->filled('max_price')) {
                $query->where('price', '<=', $request->get('max_price'));
            }
            
            // Apply ordering
            $orderBy = $request->get('order_by', 'name');
            $direction = $request->get('direction', 'asc');
            $query->orderBy($orderBy, $direction);
            
            // Apply limit if specified
            if ($request->filled('limit')) {
                $query->limit($request->get('limit'));
            }
            
            $foods = $query->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Advanced search completed successfully',
                'data' => $foods,
                'count' => $foods->count(),
                'filters' => $request->only(['query', 'category', 'place_of_origin', 'min_price', 'max_price'])
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
}
