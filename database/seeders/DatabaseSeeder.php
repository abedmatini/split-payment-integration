<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Phase 5 – Main seeder
 *
 * WHY: DatabaseSeeder is the entry point for db:seed. We call seeders in
 * order: products first (no dependencies), then users. This way we have
 * a consistent dev/demo dataset. Run with: php artisan db:seed
 * or migrate:fresh --seed to reset and seed in one go.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
            UserSeeder::class,
        ]);
    }
}
