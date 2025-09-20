# Coil Project API Documentation

## Table of Contents
- [Overview](#overview)
- [Project Setup](#project-setup)
- [Database Setup](#database-setup)
  - [Migrations](#migrations)
  - [Seeders](#seeders)
- [Testing](#testing)
- [API Documentation](#api-documentation)
  - [Food API](#food-api)
  - [Places API](#places-api)
- [Error Handling](#error-handling)
- [Response Format](#response-format)

## Overview

The Coil Project is a Laravel-based web application that showcases the unique and famous places and foods from Indonesia and the Philippines. This API provides comprehensive endpoints for retrieving, searching, and filtering food items and tourist places.

### Technology Stack
- **Backend**: Laravel 11
- **Database**: SQLite (for development and testing)
- **Testing**: PHPUnit with Feature Tests
- **API Format**: RESTful JSON API

## Project Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 11

### Installation
```bash
# Clone the repository
git clone <repository-url>
cd coil-app

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Database Setup

### Migrations

The project includes several database migrations that create the necessary tables:

#### 1. Run All Migrations
```bash
php artisan migrate
```

#### Migration Files Overview

**Food Table Migration** (`2025_09_18_141841_create_food_table.php`)
```php
Schema::create('food', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('place_of_origin')->nullable();
    $table->string('category')->nullable();
    $table->string('filename')->nullable();
    $table->string('caption')->nullable();
    $table->decimal('price', 8, 2)->nullable();
    $table->timestamps();
});
```

**Places Table Migration** (`2025_09_18_134852_create_places_table.php`)
```php
Schema::create('places', function (Blueprint $table) {
    $table->id('place_id');
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('latitude');
    $table->string('longitude');
    $table->string('country')->nullable();
    $table->string('location')->nullable();
    $table->enum('type', ['landmark', 'restaurant', 'heritage', 'gallery', 'museum', 'other'])->default('other');
    $table->string('caption')->nullable();
    $table->timestamps();
});
```

**Places Photo Table Migration** (`2025_09_18_135635_create_places_photo_table.php`)
```php
Schema::create('places_photo', function (Blueprint $table) {
    $table->id();
    $table->foreignId('place_id')->constrained('places', 'place_id')->onDelete('cascade');
    $table->string('caption')->nullable();
    $table->string('description')->nullable();
    $table->string('filename')->nullable();
    $table->timestamps();
});
```

### Seeders

The project includes comprehensive seeders for both food and places data:

#### 1. Run All Seeders
```bash
php artisan db:seed
```

#### 2. Run Specific Seeders
```bash
# Seed only food data
php artisan db:seed --class=FoodSeeder

# Seed only places data
php artisan db:seed --class=PlacesSeeder
```

#### Seeder Overview

**FoodSeeder**: Seeds 20 traditional Philippine food items including:
- Adobo, Sinigang, Lechon, Kare-Kare
- Detailed descriptions, pricing, categories, and place of origin
- Categories: Main Dish, Soup, Dessert, Snack, Noodles, etc.

**PlacesSeeder**: Seeds 20 famous places in the Philippines including:
- Historical sites (Intramuros, Fort Santiago)
- Natural wonders (Chocolate Hills, Mayon Volcano)  
- Museums and cultural sites
- Each place includes photos and geographical coordinates

## Testing

The project includes comprehensive test suites for both Food and Places APIs.

### Running Tests

#### Run All Tests
```bash
php artisan test
```

#### Run Specific Test Suites
```bash
# Run only Food API tests
php artisan test --filter=FoodControllerTest

# Run only Places API tests  
php artisan test --filter=PlacesControllerTest

# Run both API tests
php artisan test --filter="FoodControllerTest|PlacesControllerTest"
```

### Test Coverage

- **Food API**: 22 tests, 363 assertions
- **Places API**: 12 tests, 488 assertions
- **Total**: 34 tests, 851 assertions

Tests cover:
- ✅ All endpoint functionality
- ✅ Input validation
- ✅ Error handling
- ✅ Data integrity
- ✅ JSON response structure
- ✅ Database relationships

## API Documentation

All API endpoints return JSON responses with a consistent structure. The base URL for all endpoints is `/api`.

### Response Format

All API responses follow this structure:

```json
{
    "status": "success|error",
    "message": "Descriptive message",
    "data": "Response data (array/object)",
    "count": "Number of items (when applicable)",
    "additional_fields": "Context-specific fields"
}
```

## Food API

Base URL: `/api/food`

### Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Get all food items |
| GET | `/show/{name}` | Get specific food by name |
| GET | `/limited` | Get limited number of food items |
| GET | `/search` | Search food items |
| GET | `/category/{category}` | Get food by category |
| GET | `/place/{place}` | Get food by place of origin |
| GET | `/price-range` | Get food within price range |
| GET | `/random` | Get random food items |
| GET | `/paginated` | Get paginated food items |
| GET | `/categories` | Get all food categories |
| GET | `/places-of-origin` | Get all places of origin |
| POST | `/advanced-search` | Advanced search with filters |

### Detailed Endpoints

#### 1. Get All Food Items
**GET** `/api/food`

**Parameters:**
- `order_by` (optional): `name`, `category`, `price`, `place_of_origin`, `created_at`
- `direction` (optional): `asc`, `desc`

**Example Request:**
```bash
GET /api/food?order_by=price&direction=desc
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Food items retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Adobo",
            "description": "A savory stew of chicken or pork marinated in soy sauce, vinegar, garlic, and spices.",
            "place_of_origin": "Nationwide (Philippines)",
            "category": "Main Dish",
            "caption": "The unofficial national dish of the Philippines.",
            "price": "185.00",
            "filename": "adobo.png",
            "created_at": "2025-09-20T10:30:00.000000Z",
            "updated_at": "2025-09-20T10:30:00.000000Z"
        }
    ],
    "count": 20
}
```

#### 2. Get Specific Food by Name
**GET** `/api/food/show/{name}`

**Example Request:**
```bash
GET /api/food/show/Adobo
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Food item retrieved successfully",
    "data": {
        "id": 1,
        "name": "Adobo",
        "description": "A savory stew of chicken or pork marinated in soy sauce, vinegar, garlic, and spices.",
        "place_of_origin": "Nationwide (Philippines)",
        "category": "Main Dish",
        "caption": "The unofficial national dish of the Philippines.",
        "price": "185.00",
        "filename": "adobo.png",
        "created_at": "2025-09-20T10:30:00.000000Z",
        "updated_at": "2025-09-20T10:30:00.000000Z"
    }
}
```

#### 3. Get Limited Food Items
**GET** `/api/food/limited`

**Parameters:**
- `limit` (required): Integer between 1-100
- `order_by` (optional): Ordering column
- `direction` (optional): `asc`, `desc`

**Example Request:**
```bash
GET /api/food/limited?limit=5&order_by=price&direction=asc
```

#### 4. Search Food Items
**GET** `/api/food/search`

**Parameters:**
- `query` (required): Search term (1-255 characters)

**Example Request:**
```bash
GET /api/food/search?query=chicken
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Search completed successfully",
    "data": [
        {
            "id": 13,
            "name": "Inasal",
            "description": "Grilled chicken marinated in calamansi, vinegar, and annatto oil.",
            "place_of_origin": "Bacolod",
            "category": "Main Dish",
            "price": "160.00"
        }
    ],
    "count": 1,
    "search_term": "chicken"
}
```

#### 5. Get Food by Category
**GET** `/api/food/category/{category}`

**Example Request:**
```bash
GET /api/food/category/Main Dish
```

#### 6. Get Food by Place of Origin
**GET** `/api/food/place/{place}`

**Example Request:**
```bash
GET /api/food/place/Pampanga
```

#### 7. Get Food by Price Range
**GET** `/api/food/price-range`

**Parameters:**
- `min_price` (required): Minimum price (numeric, ≥ 0)
- `max_price` (required): Maximum price (numeric, ≥ min_price)

**Example Request:**
```bash
GET /api/food/price-range?min_price=100&max_price=200
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Food items within price range ₱100 - ₱200 retrieved successfully",
    "data": [...],
    "count": 8,
    "price_range": {
        "min": 100,
        "max": 200
    }
}
```

#### 8. Get Random Food Items
**GET** `/api/food/random`

**Parameters:**
- `count` (optional): Number of items (1-20, default: 1)

**Example Request:**
```bash
GET /api/food/random?count=3
```

#### 9. Get Paginated Food Items
**GET** `/api/food/paginated`

**Parameters:**
- `per_page` (optional): Items per page (1-50, default: 10)

**Example Request:**
```bash
GET /api/food/paginated?per_page=5
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Paginated food items retrieved successfully",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "last_page": 4,
        "per_page": 5,
        "total": 20,
        "from": 1,
        "to": 5,
        "has_more_pages": true
    }
}
```

#### 10. Get Food Categories
**GET** `/api/food/categories`

**Sample Response:**
```json
{
    "status": "success",
    "message": "Food categories retrieved successfully",
    "data": [
        "Main Dish",
        "Soup", 
        "Dessert",
        "Snack",
        "Noodles",
        "Breakfast Dish",
        "Snack/Appetizer",
        "Snack/Dessert"
    ],
    "count": 8
}
```

#### 11. Get Places of Origin
**GET** `/api/food/places-of-origin`

**Sample Response:**
```json
{
    "status": "success",
    "message": "Places of origin retrieved successfully",
    "data": [
        "Nationwide (Philippines)",
        "Cebu",
        "Pampanga",
        "Bicol Region",
        "Batangas",
        "Iloilo",
        "Bacolod"
    ],
    "count": 7
}
```

#### 12. Advanced Search
**POST** `/api/food/advanced-search`

**Request Body Parameters:**
- `query` (optional): Search term
- `category` (optional): Food category
- `place_of_origin` (optional): Place of origin
- `min_price` (optional): Minimum price
- `max_price` (optional): Maximum price
- `order_by` (optional): Ordering column
- `direction` (optional): `asc`, `desc`
- `limit` (optional): Result limit (1-100)

**Example Request:**
```bash
POST /api/food/advanced-search
Content-Type: application/json

{
    "query": "chicken",
    "category": "Main Dish",
    "min_price": 100,
    "max_price": 300,
    "order_by": "price",
    "direction": "asc",
    "limit": 10
}
```

## Places API

Base URL: `/api/places`

### Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Get all places with photos |
| GET | `/show/{name}` | Get specific place by name |
| GET | `/id/{id}` | Get specific place by ID |
| GET | `/limited` | Get limited number of places |
| GET | `/search` | Search places |
| GET | `/type/{type}` | Get places by type |
| GET | `/country/{country}` | Get places by country |
| GET | `/bounds` | Get places within coordinate bounds |
| POST | `/set` | Get set of places by IDs |
| GET | `/random` | Get random places |
| GET | `/paginated` | Get paginated places |
| GET | `/types` | Get all place types |
| GET | `/countries` | Get all countries |
| POST | `/advanced-search` | Advanced search with filters |

### Detailed Endpoints

#### 1. Get All Places with Photos
**GET** `/api/places`

**Parameters:**
- `order_by` (optional): `name`, `type`, `country`, `location`, `created_at`
- `direction` (optional): `asc`, `desc`

**Sample Response:**
```json
{
    "status": "success",
    "message": "Places with photos retrieved successfully",
    "data": [
        {
            "place_id": 1,
            "name": "Banaue Rice Terraces",
            "description": "Often called the 'Eighth Wonder of the World,' these 2,000-year-old terraces were carved into the mountains by the Ifugao people.",
            "latitude": "16.9333",
            "longitude": "121.1333",
            "country": "Philippines",
            "location": "Banaue, Ifugao, Philippines",
            "type": "heritage",
            "caption": "Ancient hand-carved terraces of breathtaking beauty.",
            "created_at": "2025-09-20T10:30:00.000000Z",
            "updated_at": "2025-09-20T10:30:00.000000Z",
            "photos": [
                {
                    "id": 1,
                    "place_id": 1,
                    "caption": "Stunning aerial view of the terraces",
                    "description": "Panoramic view of the ancient rice terraces",
                    "filename": "banaue_rice_terraces.jpg",
                    "created_at": "2025-09-20T10:30:00.000000Z",
                    "updated_at": "2025-09-20T10:30:00.000000Z"
                }
            ]
        }
    ],
    "count": 20
}
```

#### 2. Get Specific Place by Name
**GET** `/api/places/show/{name}`

**Example Request:**
```bash
GET /api/places/show/Intramuros
```

#### 3. Get Specific Place by ID
**GET** `/api/places/id/{id}`

**Example Request:**
```bash
GET /api/places/id/1
```

#### 4. Get Limited Places
**GET** `/api/places/limited`

**Parameters:**
- `limit` (required): Integer between 1-100
- `order_by` (optional): Ordering column
- `direction` (optional): `asc`, `desc`

#### 5. Search Places
**GET** `/api/places/search`

**Parameters:**
- `query` (required): Search term

**Example Request:**
```bash
GET /api/places/search?query=Manila
```

#### 6. Get Places by Type
**GET** `/api/places/type/{type}`

**Valid Types:**
- `landmark`
- `restaurant` 
- `heritage`
- `gallery`
- `museum`
- `other`

**Example Request:**
```bash
GET /api/places/type/heritage
```

#### 7. Get Places by Country
**GET** `/api/places/country/{country}`

**Example Request:**
```bash
GET /api/places/country/Philippines
```

#### 8. Get Places within Coordinate Bounds
**GET** `/api/places/bounds`

**Parameters:**
- `min_lat` (required): Minimum latitude (-90 to 90)
- `max_lat` (required): Maximum latitude (≥ min_lat)
- `min_lng` (required): Minimum longitude (-180 to 180)
- `max_lng` (required): Maximum longitude (≥ min_lng)

**Example Request:**
```bash
GET /api/places/bounds?min_lat=10.0&max_lat=18.0&min_lng=118.0&max_lng=125.0
```

**Sample Response:**
```json
{
    "status": "success",
    "message": "Places within coordinate bounds with photos retrieved successfully",
    "data": [...],
    "count": 15,
    "bounds": {
        "min_lat": 10.0,
        "max_lat": 18.0,
        "min_lng": 118.0,
        "max_lng": 125.0
    }
}
```

#### 9. Get Set of Places by IDs
**POST** `/api/places/set`

**Request Body:**
```json
{
    "place_ids": [1, 2, 3, 4, 5]
}
```

**Parameters:**
- `place_ids` (required): Array of place IDs (1-50 items, each must exist)

#### 10. Get Random Places
**GET** `/api/places/random`

**Parameters:**
- `count` (optional): Number of items (1-20, default: 1)

#### 11. Get Paginated Places
**GET** `/api/places/paginated`

**Parameters:**
- `per_page` (optional): Items per page (1-50, default: 10)

#### 12. Get Place Types
**GET** `/api/places/types`

**Sample Response:**
```json
{
    "status": "success",
    "message": "Place types retrieved successfully",
    "data": [
        "heritage",
        "museum",
        "other"
    ],
    "count": 3
}
```

#### 13. Get Countries
**GET** `/api/places/countries`

**Sample Response:**
```json
{
    "status": "success",
    "message": "Countries retrieved successfully",
    "data": [
        "Philippines"
    ],
    "count": 1
}
```

#### 14. Advanced Search
**POST** `/api/places/advanced-search`

**Request Body Parameters:**
- `name` (optional): Place name
- `type` (optional): Place type
- `country` (optional): Country name
- `search_term` (optional): General search term
- `order_by` (optional): Ordering column
- `order_direction` (optional): `asc`, `desc`
- `limit` (optional): Result limit (1-100)

**Example Request:**
```bash
POST /api/places/advanced-search
Content-Type: application/json

{
    "search_term": "Manila",
    "type": "heritage",
    "country": "Philippines",
    "order_by": "name",
    "order_direction": "asc",
    "limit": 10
}
```

## Error Handling

### HTTP Status Codes

- **200 OK**: Successful request
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server error

### Error Response Format

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

### Common Validation Errors

**Missing Required Parameters:**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "limit": ["The limit field is required."],
        "query": ["The query field is required."]
    }
}
```

**Invalid Parameter Values:**
```json
{
    "status": "error", 
    "message": "Validation failed",
    "errors": {
        "limit": ["The limit must be between 1 and 100."],
        "max_price": ["The max price must be greater than or equal to min price."]
    }
}
```

## Testing API Endpoints

### Using cURL

**Get all food items:**
```bash
curl -X GET "http://localhost:8000/api/food" \
     -H "Accept: application/json"
```

**Search for food:**
```bash
curl -X GET "http://localhost:8000/api/food/search?query=chicken" \
     -H "Accept: application/json"
```

**Advanced food search:**
```bash
curl -X POST "http://localhost:8000/api/food/advanced-search" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"category": "Main Dish", "min_price": 100, "max_price": 300}'
```

### Development Server

Start the Laravel development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

## Conclusion

This API provides comprehensive endpoints for managing and retrieving food and places data for the Philippines tourism project. The API is fully tested, documented, and ready for production use. All endpoints follow RESTful conventions and return consistent JSON responses with proper error handling.

For additional support or questions, please refer to the test files located in `tests/Feature/` for usage examples and expected behavior.