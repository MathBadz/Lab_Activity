<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if products already exist to prevent duplicates on redeploy
        if (Product::count() > 0) {
            return;
        }

        $products = [
            ['name' => 'Elden Ring', 'category' => 'Game', 'image' => '/images/products/eldenring.jpg', 'price' => 3490.00, 'stock' => 15],
            ['name' => 'God of War Ragnarök', 'category' => 'Game', 'image' => '/images/products/gowr.jpg', 'price' => 3490.00, 'stock' => 20],
            ['name' => 'Spider-Man 2', 'category' => 'Game', 'image' => '/images/products/spiderman2.png', 'price' => 3490.00, 'stock' => 25],
            ['name' => 'Final Fantasy VII Rebirth', 'category' => 'Game', 'image' => '/images/products/finalfantasy.png', 'price' => 3490.00, 'stock' => 10],
            ['name' => 'Resident Evil 4 Remake', 'category' => 'Game', 'image' => '/images/products/residentevil4.png', 'price' => 2990.00, 'stock' => 8],
            ['name' => 'Horizon Forbidden West', 'category' => 'Game', 'image' => '/images/products/horizon.png', 'price' => 2490.00, 'stock' => 30],
            ['name' => 'Call of Duty: Modern Warfare III', 'category' => 'Game', 'image' => '/images/products/cod.png', 'price' => 3490.00, 'stock' => 50],
            ['name' => 'NBA 2K24', 'category' => 'Game', 'image' => '/images/products/nba.png', 'price' => 3490.00, 'stock' => 40],
            ['name' => 'Gran Turismo 7', 'category' => 'Game', 'image' => '/images/products/granturismo.png', 'price' => 3490.00, 'stock' => 12],
            ['name' => 'Tekken 8', 'category' => 'Game', 'image' => '/images/products/tekken.png', 'price' => 3490.00, 'stock' => 18],
            ['name' => 'Ghost of Tsushima Director\'s Cut', 'category' => 'Game', 'image' => '/images/products/gotd.png', 'price' => 3490.00, 'stock' => 14],
            ['name' => 'DualSense Wireless Controller', 'category' => 'Peripheral', 'image' => '/images/products/controller.png', 'price' => 3990.00, 'stock' => 100],
            ['name' => 'Pulse 3D Headset', 'category' => 'Peripheral', 'image' => '/images/products/headset.png', 'price' => 4990.00, 'stock' => 45],
            ['name' => 'PS5 Charging Dock', 'category' => 'Peripheral', 'image' => '/images/products/charging.png', 'price' => 1690.00, 'stock' => 60],
            ['name' => 'Sony INZONE M9 Gaming Monitor', 'category' => 'Hardware', 'image' => '/images/products/monitor.png', 'price' => 45990.00, 'stock' => 5],
            ['name' => 'PlayStation Edition Gaming Chair', 'category' => 'Furniture', 'image' => '/images/products/chair.png', 'price' => 15990.00, 'stock' => 0],
        ];

        $now = now();

        Product::insert(array_map(fn ($p) => [
            ...$p,
            'created_at' => $now,
            'updated_at' => $now,
        ], $products));
    }
}
