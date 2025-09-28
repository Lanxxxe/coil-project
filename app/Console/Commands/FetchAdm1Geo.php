<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchAdm1Geo extends Command
{
    protected $signature = 'geo:fetch-adm1 {--force : Overwrite existing local files}';
    protected $description = 'Download ADM1 GeoJSON for Philippines and Indonesia into public/data/geo';

    public function handle(): int
    {
        $targets = [
            'ph' => [
                'file' => public_path('data/geo/ph_adm1.geojson'),
                'urls' => [
                    'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
                    'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
                    'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
                ],
            ],
            'id' => [
                'file' => public_path('data/geo/id_adm1.geojson'),
                'urls' => [
                    'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
                    'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
                    'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
                ],
            ],
        ];

        $dir = public_path('data/geo');
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $downloaded = 0;
        foreach ($targets as $code => $cfg) {
            $file = $cfg['file'];
            if (is_file($file) && !$this->option('force')) {
                $this->info(strtoupper($code) . ': exists -> ' . $file);
                continue;
            }
            $ok = false;
            foreach ($cfg['urls'] as $url) {
                $this->line('Fetching ' . strtoupper($code) . ' from ' . $url);
                $raw = @file_get_contents($url, false, stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 20,
                        'header' => [
                            'User-Agent: coil-project/1.0',
                            'Accept: application/geo+json, application/json;q=0.9, */*;q=0.8',
                        ],
                    ],
                ]));
                if ($raw !== false && strlen($raw) > 0) {
                    @file_put_contents($file, $raw);
                    $this->info('Saved -> ' . $file . ' (' . strlen($raw) . ' bytes)');
                    $ok = true;
                    $downloaded++;
                    break;
                }
            }
            if (!$ok) {
                $this->error('Failed to fetch ' . strtoupper($code) . ' ADM1.');
            }
        }

        $this->info('Done. Downloaded: ' . $downloaded);
        return $downloaded > 0 ? self::SUCCESS : self::FAILURE;
    }
}
