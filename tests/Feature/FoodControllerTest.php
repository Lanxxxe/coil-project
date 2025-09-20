<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Food;
use Database\Seeders\FoodSeeder;

class FoodControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with food data for testing
        $this->seed(FoodSeeder::class);
    }

    /**
     * Test getting all food items with default ordering
     */
    public function test_index_returns_all_food_items(): void
    {
        $response = $this->getJson('/api/food');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'place_of_origin',
                            'category',
                            'price',
                            'caption',
                            'filename',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'count'
                ])
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Food items retrieved successfully'
                ]);

        $this->assertTrue($response->json('count') > 0);
    }

    /**
     * Test getting all food items with custom ordering
     */
    public function test_index_with_custom_ordering(): void
    {
        $response = $this->getJson('/api/food?order_by=price&direction=desc');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ]);

        // Check if data is sorted by price descending
        $data = $response->json('data');
        if (count($data) > 1) {
            $this->assertGreaterThanOrEqual($data[1]['price'], $data[0]['price']);
        }
    }

    /**
     * Test getting specific food by name
     */
    public function test_show_returns_specific_food_by_name(): void
    {
        $response = $this->getJson('/api/food/show/Adobo');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'place_of_origin',
                        'category',
                        'price'
                    ]
                ])
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Food item retrieved successfully',
                    'data' => [
                        'name' => 'Adobo'
                    ]
                ]);
    }

    /**
     * Test getting non-existent food returns 404
     */
    public function test_show_returns_404_for_non_existent_food(): void
    {
        $response = $this->getJson('/api/food/show/NonExistentFood');

        $response->assertStatus(404)
                ->assertJson([
                    'status' => 'error',
                    'data' => null
                ]);
    }

    /**
     * Test getting limited number of food items
     */
    public function test_get_limited_returns_specified_number_of_items(): void
    {
        $limit = 5;
        $response = $this->getJson("/api/food/limited?limit={$limit}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'count' => $limit,
                    'limit' => $limit
                ]);

        $this->assertCount($limit, $response->json('data'));
    }

    /**
     * Test get limited with validation error
     */
    public function test_get_limited_validation_error(): void
    {
        $response = $this->getJson('/api/food/limited?limit=invalid');

        $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Validation failed'
                ])
                ->assertJsonStructure([
                    'errors'
                ]);
    }

    /**
     * Test search functionality
     */
    public function test_search_returns_matching_food_items(): void
    {
        $searchTerm = 'chicken';
        $response = $this->getJson("/api/food/search?query={$searchTerm}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Search completed successfully',
                    'search_term' => $searchTerm
                ])
                ->assertJsonStructure([
                    'data',
                    'count'
                ]);
    }

    /**
     * Test search with empty query validation
     */
    public function test_search_validation_error_empty_query(): void
    {
        $response = $this->getJson('/api/food/search');

        $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Validation failed'
                ]);
    }

    /**
     * Test getting food by category
     */
    public function test_get_by_category_returns_correct_items(): void
    {
        $category = 'Main Dish';
        $response = $this->getJson("/api/food/category/{$category}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'category' => $category
                ]);

        // Verify all returned items belong to the specified category
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertEquals($category, $item['category']);
        }
    }

    /**
     * Test getting food by place of origin
     */
    public function test_get_by_place_returns_correct_items(): void
    {
        $place = 'Pampanga';
        $response = $this->getJson("/api/food/place/{$place}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'place_of_origin' => $place
                ]);
    }

    /**
     * Test getting food by price range
     */
    public function test_get_by_price_range_returns_items_in_range(): void
    {
        $minPrice = 100;
        $maxPrice = 200;
        $response = $this->getJson("/api/food/price-range?min_price={$minPrice}&max_price={$maxPrice}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ])
                ->assertJsonStructure([
                    'price_range' => [
                        'min',
                        'max'
                    ]
                ]);

        // Verify all returned items are within the price range
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertGreaterThanOrEqual($minPrice, $item['price']);
            $this->assertLessThanOrEqual($maxPrice, $item['price']);
        }
    }

    /**
     * Test price range validation errors
     */
    public function test_price_range_validation_errors(): void
    {
        // Test missing parameters
        $response = $this->getJson('/api/food/price-range');
        $response->assertStatus(422);

        // Test invalid max_price (less than min_price)
        $response = $this->getJson('/api/food/price-range?min_price=200&max_price=100');
        $response->assertStatus(422);
    }

    /**
     * Test getting random food items
     */
    public function test_get_random_returns_random_items(): void
    {
        $count = 3;
        $response = $this->getJson("/api/food/random?count={$count}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Random food items retrieved successfully',
                    'requested_count' => $count
                ]);

        $this->assertLessThanOrEqual($count, $response->json('count'));
    }

    /**
     * Test getting paginated food items
     */
    public function test_get_paginated_returns_paginated_data(): void
    {
        $perPage = 5;
        $response = $this->getJson("/api/food/paginated?per_page={$perPage}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Paginated food items retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data',
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ]
                ]);

        $this->assertLessThanOrEqual($perPage, count($response->json('data')));
    }

    /**
     * Test getting all categories
     */
    public function test_get_categories_returns_unique_categories(): void
    {
        $response = $this->getJson('/api/food/categories');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Food categories retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data',
                    'count'
                ]);

        $categories = $response->json('data');
        $this->assertTrue(count($categories) > 0);
        // Verify uniqueness
        $this->assertEquals(count($categories), count(array_unique($categories)));
    }

    /**
     * Test getting all places of origin
     */
    public function test_get_places_of_origin_returns_unique_places(): void
    {
        $response = $this->getJson('/api/food/places-of-origin');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Places of origin retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data',
                    'count'
                ]);

        $places = $response->json('data');
        $this->assertTrue(count($places) > 0);
    }

    /**
     * Test advanced search with multiple filters
     */
    public function test_advanced_search_with_multiple_filters(): void
    {
        $searchData = [
            'query' => 'chicken',
            'category' => 'Main Dish',
            'min_price' => 100,
            'max_price' => 300,
            'order_by' => 'price',
            'direction' => 'asc',
            'limit' => 10
        ];

        $response = $this->postJson('/api/food/advanced-search', $searchData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ])
                ->assertJsonStructure([
                    'data',
                    'count',
                    'filters'
                ]);

        // Verify filters are applied correctly
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertEquals($searchData['category'], $item['category']);
            $this->assertGreaterThanOrEqual($searchData['min_price'], $item['price']);
            $this->assertLessThanOrEqual($searchData['max_price'], $item['price']);
        }
    }

    /**
     * Test advanced search with only query parameter
     */
    public function test_advanced_search_with_query_only(): void
    {
        $searchData = [
            'query' => 'rice'
        ];

        $response = $this->postJson('/api/food/advanced-search', $searchData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ]);
    }

    /**
     * Test advanced search validation errors
     */
    public function test_advanced_search_validation_errors(): void
    {
        $invalidData = [
            'min_price' => 'invalid',
            'max_price' => -10,
            'limit' => 150 // Exceeds max limit
        ];

        $response = $this->postJson('/api/food/advanced-search', $invalidData);

        $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Validation failed'
                ]);
    }

    /**
     * Test API endpoints handle server errors gracefully
     */
    public function test_api_handles_server_errors(): void
    {
        // Test with extremely large limit that might cause issues
        $response = $this->getJson('/api/food/limited?limit=999999');
        
        // Should either return validation error or handle gracefully
        $this->assertContains($response->status(), [422, 500]);
    }

    /**
     * Test that all endpoints return proper JSON structure
     */
    public function test_all_endpoints_return_proper_json_structure(): void
    {
        $endpoints = [
            '/api/food',
            '/api/food/categories',
            '/api/food/places-of-origin',
            '/api/food/random',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            
            $response->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);
        }
    }

    /**
     * Test URL encoding in food names
     */
    public function test_show_with_url_encoded_names(): void
    {
        // Test with food name that might need URL encoding
        $foodName = 'Kare-Kare';
        $response = $this->getJson('/api/food/show/' . urlencode($foodName));

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'name' => $foodName
                    ]
                ]);
    }
}