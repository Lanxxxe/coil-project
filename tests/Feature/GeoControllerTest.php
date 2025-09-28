<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class GeoControllerTest extends TestCase
{
    protected string $geoDir;
    protected string $phFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geoDir = public_path('data/geo');
        $this->phFile = $this->geoDir . '/ph_adm1.geojson';

        if (!is_dir($this->geoDir)) {
            @mkdir($this->geoDir, 0775, true);
        }

        // Minimal valid GeoJSON FeatureCollection with 1 polygon (degenerate) just for format
        $stub = json_encode([
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [ 'name' => 'Test Region' ],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [[[0,0],[0,1],[1,1],[1,0],[0,0]]],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES);

        file_put_contents($this->phFile, $stub);
    }

    protected function tearDown(): void
    {
        // Clean up the stub file
        if (is_file($this->phFile)) {
            @unlink($this->phFile);
        }
        parent::tearDown();
    }

    public function test_adm1_endpoint_serves_ph_geojson_from_local_override(): void
    {
        $resp = $this->getJson('/api/geo/adm1?country=ph');
        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'application/geo+json');
        $data = $resp->json();
        $this->assertIsArray($data);
        $this->assertSame('FeatureCollection', $data['type'] ?? null);
        $this->assertNotEmpty($data['features'] ?? []);
        $this->assertSame('Test Region', $data['features'][0]['properties']['name'] ?? null);
    }

    public function test_adm1_endpoint_rejects_unsupported_country(): void
    {
        $resp = $this->getJson('/api/geo/adm1?country=xx');
        $resp->assertStatus(400);
        $resp->assertJson(['error' => 'Unsupported country']);
    }
}
