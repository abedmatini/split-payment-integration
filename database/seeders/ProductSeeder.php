<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Phase 5 – Product seeder
 *
 * WHY: Seeders populate the database with known data for development and
 * testing. We use insert() or create() so the same seeder can be run
 * multiple times without duplicating data (e.g. firstOrCreate, or
 * run only when table is empty). Products are the catalog the app needs
 * to display and add to cart. Prices in ZAR match our MVP (South African).
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'Wireless Headphones',
                'description' => 'Noise-cancelling over-ear headphones with 30-hour battery life.',
                'price' => 899.99,
            ],
            [
                'title' => 'USB-C Hub',
                'description' => '7-in-1 adapter with HDMI, USB 3.0, and SD card reader.',
                'price' => 349.00,
            ],
            [
                'title' => 'Mechanical Keyboard',
                'description' => 'RGB backlit, Cherry MX-style switches, wired.',
                'price' => 649.00,
            ],
            [
                'title' => 'Laptop Stand',
                'description' => 'Aluminium adjustable stand for better ergonomics.',
                'price' => 299.00,
            ],
            [
                'title' => 'Webcam 1080p',
                'description' => 'Full HD webcam with built-in microphone and privacy shutter.',
                'price' => 499.00,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
