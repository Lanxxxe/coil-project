<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Places;
use Database\Seeders\PlacesSeeder;

class PlacesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with places data for testing
        $this->seed(PlacesSeeder::class);
    }

    /**
     * Test getting all places with default ordering
     */
    public function test_index_returns_all_places_with_photos(): void
    {
        $response = $this->getJson('/api/places');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        '*' => [
                            'place_id',
                            'name',
                            'description',
                            'latitude',
                            'longitude',
                            'country',
                            'location',
                            'type',
                            'caption',
                            'created_at',
                            'updated_at',
                            'photos' => [
                                '*' => [
                                    'id',
                                    'place_id',
                                    'caption',
                                    'description',
                                    'filename',
                                    'created_at',
                                    'updated_at'
                                ]
                            ]
                        ]
                    ],
                    'count'
                ])
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Places with photos retrieved successfully'
                ]);

        $this->assertTrue($response->json('count') > 0);
    }

    /**
     * Test getting specific place by name
     */
    public function test_show_returns_specific_place_by_name(): void
    {
        $response = $this->getJson('/api/places/show/Intramuros');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'place_id',
                        'name',
                        'description',
                        'latitude',
                        'longitude',
                        'country',
                        'type',
                        'photos'
                    ]
                ])
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Place with photos retrieved successfully',
                    'data' => [
                        'name' => 'Intramuros'
                    ]
                ]);
    }

    /**
     * Test getting limited number of places
     */
    public function test_get_limited_returns_specified_number_of_items(): void
    {
        $limit = 5;
        $response = $this->getJson("/api/places/limited?limit={$limit}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'count' => $limit,
                    'limit' => $limit
                ]);

        $this->assertCount($limit, $response->json('data'));
    }

    /**
     * Test search functionality
     */
    public function test_search_returns_matching_places(): void
    {
        $searchTerm = 'Manila';
        $response = $this->getJson("/api/places/search?query={$searchTerm}");

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
     * Test getting places by type
     */
    public function test_get_by_type_returns_correct_items(): void
    {
        $type = 'heritage';
        $response = $this->getJson("/api/places/type/{$type}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'type' => $type
                ]);

        // Verify all returned items belong to the specified type
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertEquals($type, $item['type']);
        }
    }

    /**
     * Test getting all types
     */
    public function test_get_types_returns_unique_types(): void
    {
        $response = $this->getJson('/api/places/types');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Place types retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data',
                    'count'
                ]);

        $types = $response->json('data');
        $this->assertTrue(count($types) > 0);
    }

    /**
     * Test getting all countries
     */
    public function test_get_countries_returns_unique_countries(): void
    {
        $response = $this->getJson('/api/places/countries');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Countries retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data',
                    'count'
                ]);

        $countries = $response->json('data');
        $this->assertTrue(count($countries) > 0);
    }

    /**
     * Test getting random places
     */
    public function test_get_random_returns_random_items(): void
    {
        $count = 3;
        $response = $this->getJson("/api/places/random?count={$count}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Random places with photos retrieved successfully',
                    'requested_count' => $count
                ]);

        $this->assertLessThanOrEqual($count, $response->json('count'));
    }

    /**
     * Test coordinate bounds validation errors
     */
    public function test_bounds_validation_errors(): void
    {
        // Test missing parameters
        $response = $this->getJson('/api/places/bounds');
        $response->assertStatus(422);

        // Test invalid coordinates (max less than min)
        $response = $this->getJson('/api/places/bounds?min_lat=20&max_lat=10&min_lng=100&max_lng=120');
        $response->assertStatus(422);
    }

    /**
     * Test advanced search with search term only
     */
    public function test_advanced_search_with_search_term_only(): void
    {
        $searchData = [
            'search_term' => 'museum'
        ];

        $response = $this->postJson('/api/places/advanced-search', $searchData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ]);
    }

    /**
     * Test that photos relationship is loaded correctly
     */
    public function test_places_include_photos_relationship(): void
    {
        $response = $this->getJson('/api/places');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        if (count($data) > 0) {
            $this->assertArrayHasKey('photos', $data[0]);
            $this->assertIsArray($data[0]['photos']);
        }
    }

    /**
     * Test place types are valid enum values
     */
    public function test_place_types_are_valid_enum_values(): void
    {
        $validTypes = ['landmark', 'restaurant', 'heritage', 'gallery', 'museum', 'other'];
        
        $response = $this->getJson('/api/places');
        $response->assertStatus(200);
        
        $data = $response->json('data');
        foreach ($data as $place) {
            $this->assertContains($place['type'], $validTypes);
        }
    }
}