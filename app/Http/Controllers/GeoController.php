<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GeoController extends Controller
{
    /**
     * Serve ADM1 GeoJSON for supported countries via same-origin to avoid CORS.
     * Accepts ?country=ph|id. Caches the fetched file under storage/app/geo.
     */
    public function adm1(Request $request)
    {
        $country = strtolower($request->query('country', ''));
        if (!in_array($country, ['ph', 'id'])) {
            return response()->json(['error' => 'Unsupported country'], 400)
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Local override under public/ if provided
        $localPath = public_path("data/geo/{$country}_adm1.geojson");
        if (is_file($localPath)) {
            $json = @file_get_contents($localPath) ?: '';
            return response($json, 200)
                ->header('Content-Type', 'application/geo+json')
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Cached copy under storage/app/geo
        $cacheDir = storage_path('app/geo');
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
        $cacheFile = $cacheDir . "/{$country}_adm1.geojson";
        if (is_file($cacheFile)) {
            $json = @file_get_contents($cacheFile) ?: '';
            return response($json, 200)
                ->header('Content-Type', 'application/geo+json')
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Remote candidates (CORS-friendly CDNs)
        $urls = [];
        if ($country === 'ph') {
            $urls = [
                'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
                'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
                'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
            ];
        } else {
            $urls = [
                'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
                'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
                'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
            ];
        }

        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => 12,
                'header' => [
                    'User-Agent: coil-project/1.0',
                    'Accept: application/geo+json, application/json;q=0.9, */*;q=0.8',
                ],
            ],
        ];
        $context = stream_context_create($opts);
        foreach ($urls as $url) {
            $raw = @file_get_contents($url, false, $context);
            if ($raw === false || strlen($raw) === 0) continue;
            // Validate JSON and ensure it's a FeatureCollection, skip Git LFS pointers
            $decoded = @json_decode($raw, true);
            if (!is_array($decoded) || ($decoded['type'] ?? '') !== 'FeatureCollection') {
                // Not valid GeoJSON content; try next URL
                continue;
            }
            // Cache and return
            @file_put_contents($cacheFile, json_encode($decoded));
            return response(json_encode($decoded), 200)
                ->header('Content-Type', 'application/geo+json')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('X-Geo-Source', $url);
        }

        return response()->json(['error' => 'Failed to fetch ADM1 GeoJSON'], 502)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
