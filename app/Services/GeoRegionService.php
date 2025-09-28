<?php

namespace App\Services;

class GeoRegionService
{
    /** @var array<string, array> */
    protected static array $cache = [];

    /**
     * Load GeoJSON for a country code ('ph' or 'id').
     * Returns decoded array or null on failure.
     */
    public static function loadCountryGeo(string $countryCode): ?array
    {
        $countryCode = strtolower($countryCode);
        $key = "geo_{$countryCode}";
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $file = null;
        if ($countryCode === 'ph') {
            $file = public_path('data/geo/ph_regions.min.geojson');
        } elseif ($countryCode === 'id') {
            $file = public_path('data/geo/id_provinces.min.geojson');
        }
        if (!$file || !is_file($file)) {
            return null;
        }
        $raw = @file_get_contents($file);
        if ($raw === false) return null;
        $json = json_decode($raw, true);
        if (!is_array($json)) return null;
        self::$cache[$key] = $json;
        return $json;
    }

    /**
     * Find a feature by region name (case-insensitive).
     * Returns the feature array or null.
     */
    public static function findRegionFeature(string $countryCode, string $regionName): ?array
    {
        $data = self::loadCountryGeo($countryCode);
        if (!$data || !isset($data['features']) || !is_array($data['features'])) return null;
        $target = strtolower(self::canonicalize($countryCode, $regionName));
        foreach ($data['features'] as $f) {
            $name = strtolower(self::canonicalize($countryCode, (string)($f['properties']['name'] ?? '')));
            if ($name === $target) return $f;
        }
        // fuzzy contains fallback
        foreach ($data['features'] as $f) {
            $name = strtolower(self::canonicalize($countryCode, (string)($f['properties']['name'] ?? '')));
            if ($name !== '' && str_contains($name, $target)) return $f;
        }
        return null;
    }

    /**
     * Compute bbox [minLng, minLat, maxLng, maxLat] for a Polygon or MultiPolygon.
     */
    public static function bbox(array $geom): ?array
    {
        $type = $geom['type'] ?? null;
        $coords = $geom['coordinates'] ?? null;
        if (!$type || !is_array($coords)) return null;
        $minLng = 180; $minLat = 90; $maxLng = -180; $maxLat = -90;
        $scan = function(array $pts) use (&$minLng, &$minLat, &$maxLng, &$maxLat) {
            foreach ($pts as $pt) {
                if (!is_array($pt) || count($pt) < 2) continue;
                $lng = (float)$pt[0];
                $lat = (float)$pt[1];
                if ($lng < $minLng) $minLng = $lng;
                if ($lat < $minLat) $minLat = $lat;
                if ($lng > $maxLng) $maxLng = $lng;
                if ($lat > $maxLat) $maxLat = $lat;
            }
        };
        if ($type === 'Polygon') {
            foreach ($coords as $ring) { $scan($ring); }
        } elseif ($type === 'MultiPolygon') {
            foreach ($coords as $poly) {
                foreach ($poly as $ring) { $scan($ring); }
            }
        } else {
            return null;
        }
        return [$minLng, $minLat, $maxLng, $maxLat];
    }

    /**
     * Point in polygon (ray casting) for a single ring. Coords in [lng,lat].
     */
    protected static function pointInRing(float $lng, float $lat, array $ring): bool
    {
        $inside = false;
        $n = count($ring);
        if ($n < 3) return false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = (float)$ring[$i][0]; $yi = (float)$ring[$i][1];
            $xj = (float)$ring[$j][0]; $yj = (float)$ring[$j][1];
            $intersect = (($yi > $lat) !== ($yj > $lat)) &&
                ($lng < ($xj - $xi) * ($lat - $yi) / (max($yj - $yi, 1e-12)) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }

    /**
     * Point in Polygon / MultiPolygon respecting holes.
     */
    public static function pointInGeometry(float $lng, float $lat, array $geom): bool
    {
        $type = $geom['type'] ?? null;
        $coords = $geom['coordinates'] ?? null;
        if ($type === 'Polygon') {
            return self::pointInPolygon($lng, $lat, $coords);
        }
        if ($type === 'MultiPolygon') {
            foreach ($coords as $poly) {
                if (self::pointInPolygon($lng, $lat, $poly)) return true;
            }
            return false;
        }
        return false;
    }

    protected static function pointInPolygon(float $lng, float $lat, array $poly): bool
    {
        if (empty($poly) || !is_array($poly[0])) return false;
        // First ring is outer, rest are holes
        if (!self::pointInRing($lng, $lat, $poly[0])) return false;
        // If in any hole, then not inside
        $holes = array_slice($poly, 1);
        foreach ($holes as $hole) {
            if (self::pointInRing($lng, $lat, $hole)) return false;
        }
        return true;
    }

    /**
     * Normalize region names with aliases, removing punctuation and collapsing spaces.
     */
    protected static function canonicalize(string $countryCode, string $name): string
    {
        $countryCode = strtolower($countryCode);
        $s = strtolower(trim($name));
        // strip punctuation
        $s = preg_replace('/[^a-z0-9\s]/', '', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;

        $aliases = [];
        if ($countryCode === 'ph') {
            $aliases = [
                'ncr' => 'national capital region',
                'metro manila' => 'national capital region',
                'cordillera administrative region' => 'cordillera administrative region',
                'car' => 'cordillera administrative region',
                'region 1' => 'ilocos region',
                'region i' => 'ilocos region',
                'ilocos' => 'ilocos region',
                'region 2' => 'cagayan valley',
                'region ii' => 'cagayan valley',
                'region 3' => 'central luzon',
                'region iii' => 'central luzon',
                'region 4a' => 'calabarzon',
                'region iva' => 'calabarzon',
                'calabarzon' => 'calabarzon',
                'region 4b' => 'mimaropa region',
                'region ivb' => 'mimaropa region',
                'mimaropa' => 'mimaropa region',
                'region 5' => 'bicol region',
                'region v' => 'bicol region',
                'bicol' => 'bicol region',
                'region 6' => 'western visayas',
                'region vi' => 'western visayas',
                'region 7' => 'central visayas',
                'region vii' => 'central visayas',
                'region 8' => 'eastern visayas',
                'region viii' => 'eastern visayas',
                'region 9' => 'zamboanga peninsula',
                'region ix' => 'zamboanga peninsula',
                'region 10' => 'northern mindanao',
                'region x' => 'northern mindanao',
                'region 11' => 'davao region',
                'region xi' => 'davao region',
                'region 12' => 'soccsksargen',
                'region xii' => 'soccsksargen',
                'region 13' => 'caraga',
                'region xiii' => 'caraga',
                'barmm' => 'bangsamoro autonomous region in muslim mindanao',
                'armm' => 'bangsamoro autonomous region in muslim mindanao',
                'bangsamoro' => 'bangsamoro autonomous region in muslim mindanao',
            ];
        } elseif ($countryCode === 'id') {
            $aliases = [
                'jakarta' => 'dki jakarta',
                'dki' => 'dki jakarta',
                'yogyakarta' => 'di yogyakarta',
                'daerah istimewa yogyakarta' => 'di yogyakarta',
                'west java' => 'jawa barat',
                'central java' => 'jawa tengah',
                'east java' => 'jawa timur',
                'banten' => 'banten',
                'bali' => 'bali',
                'west nusa tenggara' => 'nusa tenggara barat',
                'east nusa tenggara' => 'nusa tenggara timur',
                'west kalimantan' => 'kalimantan barat',
                'central kalimantan' => 'kalimantan tengah',
                'south kalimantan' => 'kalimantan selatan',
                'east kalimantan' => 'kalimantan timur',
                'north kalimantan' => 'kalimantan utara',
                'north sulawesi' => 'sulawesi utara',
                'central sulawesi' => 'sulawesi tengah',
                'south sulawesi' => 'sulawesi selatan',
                'southeast sulawesi' => 'sulawesi tenggara',
                'west sulawesi' => 'sulawesi barat',
                'gorontalo' => 'gorontalo',
                'maluku' => 'maluku',
                'north maluku' => 'maluku utara',
                'aceh' => 'aceh',
                'riau islands' => 'kepulauan riau',
                'bangka belitung' => 'kepulauan bangka belitung',
                'west sumatra' => 'sumatera barat',
                'north sumatra' => 'sumatera utara',
                'south sumatra' => 'sumatera selatan',
                'lampung' => 'lampung',
                'bengkulu' => 'bengkulu',
                'jambi' => 'jambi',
                'riau' => 'riau',
                'papua' => 'papua',
                'west papua' => 'papua barat',
                'south papua' => 'papua selatan',
                'central papua' => 'papua tengah',
                'mountain papua' => 'papua pegunungan',
                'southwest papua' => 'papua barat daya',
            ];
        }
        if (isset($aliases[$s])) return $aliases[$s];
        return $s;
    }
}
