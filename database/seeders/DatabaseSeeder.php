<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@rsms.local');
        $password = env('ADMIN_PASSWORD', 'ChangeThis123!');

        // Upsert admin
        $existing = DB::table('admins')->where('email', $email)->first();
        if (!$existing) {
            DB::table('admins')->insert([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed regions and districts from JSON location files
        $this->call([
            MwanzaLocationsSeeder::class,
            DarEsSalaamLocationsSeeder::class,
            ArushaLocationsSeeder::class,
            DodomaLocationsSeeder::class,
            GeitaLocationsSeeder::class,
            IringaLocationsSeeder::class,
            KageraLocationsSeeder::class,
            KataviLocationsSeeder::class,
            KigomaLocationsSeeder::class,
            KilimanjaroLocationsSeeder::class,
            ManyaraLocationsSeeder::class,
            LindiLocationsSeeder::class,
            TangaLocationsSeeder::class,
            TaboraLocationsSeeder::class,
            SongweLocationsSeeder::class,
            SingidaLocationsSeeder::class,
            SimiyuLocationsSeeder::class,
            ShinyangaLocationsSeeder::class,
            RuvumaLocationsSeeder::class,
            RukwaLocationsSeeder::class,
            MtwaraLocationsSeeder::class,
            PwaniLocationsSeeder::class,
            NjombeLocationsSeeder::class,
            MaraLocationsSeeder::class,
            MbeyaLocationsSeeder::class,
            MorogoroLocationsSeeder::class,
        ]);
    }
}
