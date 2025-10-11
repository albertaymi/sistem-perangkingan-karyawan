<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Jalankan semua seeder untuk data awal sistem
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            KriteriaSeeder::class,
        ]);
    }
}
