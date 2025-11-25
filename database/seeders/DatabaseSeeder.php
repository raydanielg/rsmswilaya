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

        // Seed Mwanza regions and districts from JSON
        $this->call([
            MwanzaLocationsSeeder::class,
        ]);
    }
}
