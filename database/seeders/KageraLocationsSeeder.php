<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KageraLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('views/locations/kagera.json');
        if (!file_exists($path)) {
            $this->command?->warn("kagera.json not found at {$path}, skipping KageraLocationsSeeder.");
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $this->command?->error('Failed to decode kagera.json; expected an array.');
            return;
        }

        $regions = [];
        $districts = [];

        foreach ($data as $row) {
            $regionName = trim((string)($row['REGION'] ?? ''));
            $districtName = trim((string)($row['DISTRICT'] ?? ''));
            if ($regionName === '' || $districtName === '') continue;

            if (!isset($regions[$regionName])) {
                $regionId = DB::table('regions')->where('name', $regionName)->value('id');
                if (!$regionId) {
                    $regionId = DB::table('regions')->insertGetId([
                        'name' => $regionName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $regions[$regionName] = $regionId;
            }

            $regionId = $regions[$regionName];
            $districtKey = $regionId.'|'.$districtName;
            if (isset($districts[$districtKey])) continue;

            $districtId = DB::table('districts')
                ->where('region_id', $regionId)
                ->where('name', $districtName)
                ->value('id');

            if (!$districtId) {
                $districtId = DB::table('districts')->insertGetId([
                    'region_id' => $regionId,
                    'name' => $districtName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $districts[$districtKey] = $districtId;
        }

        $this->command?->info('KageraLocationsSeeder completed: '.count($regions).' region(s), '.count($districts).' district(s) processed.');
    }
}
