<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultsSeeder extends Seeder
{
    public function run(): void
    {
        // Sample regions
        $regions = ['ARUSHA','DAR ES SALAAM','DODOMA'];
        $regionIds = [];
        foreach ($regions as $name) {
            $id = DB::table('regions')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $regionIds[$name] = $id;
        }

        // Sample districts per region
        $districts = [
            'ARUSHA' => ['ARUSHA DC','ARUSHA MC','MERU'],
            'DAR ES SALAAM' => ['ILALA','KINONDONI'],
            'DODOMA' => ['DODOMA MC'],
        ];
        $districtIds = [];
        foreach ($districts as $rName => $dists) {
            foreach ($dists as $d) {
                $id = DB::table('districts')->insertGetId([
                    'region_id' => $regionIds[$rName],
                    'name' => $d,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $districtIds[$d] = $id;
            }
        }

        // Sample schools
        $schools = [
            'ARUSHA DC' => ['ABERNATHY PRIMARY SCHOOL','GREEN HILL PRIMARY'],
            'ARUSHA MC' => ['CENTRAL PRIMARY','CITY PRIMARY'],
            'MERU' => ['MERU PRIMARY'],
        ];
        $schoolIds = [];
        foreach ($schools as $dName => $ss) {
            foreach ($ss as $s) {
                $id = DB::table('schools')->insertGetId([
                    'district_id' => $districtIds[$dName],
                    'name' => $s,
                    'code' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $schoolIds[$s] = $id;
            }
        }

        // Sample result documents (SFNA years)
        $docs = [
            ['exam' => 'SFNA', 'year' => 2024, 'region' => 'ARUSHA', 'district' => 'ARUSHA DC', 'school' => 'ABERNATHY PRIMARY SCHOOL', 'file_url' => '/sample-pdfs/sfna-2024-abernathy.pdf'],
            ['exam' => 'SFNA', 'year' => 2023, 'region' => 'ARUSHA', 'district' => 'ARUSHA DC', 'school' => 'ABERNATHY PRIMARY SCHOOL', 'file_url' => '/sample-pdfs/sfna-2023-abernathy.pdf'],
        ];
        foreach ($docs as $d) {
            DB::table('result_documents')->insert([
                'exam' => $d['exam'],
                'year' => $d['year'],
                'region_id' => $regionIds[$d['region']],
                'district_id' => $districtIds[$d['district']],
                'school_id' => $schoolIds[$d['school']],
                'file_url' => $d['file_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
