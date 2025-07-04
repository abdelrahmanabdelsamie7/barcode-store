<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Enums\MaterialType;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $materials = MaterialType::values();
        $subCategories = [
            '8b325ff5-0e31-43e7-b7ab-02057b1d68a8', // Round T-shirts
            // '758c4240-eab1-477a-a28e-871ec943db34', // Polo T-shirt
            // 'a42c14a2-74c5-48fc-863f-6b536d9a987d', // Sets
            // '9ecc3af9-42da-4abd-bdec-01580530b9c1', // Sets
        ];

        for ($i = 1; $i <= 30; $i++) {
            $title = "Test Product $i";
            $slug = Str::slug($title);

            Product::create([
                'title' => $title,
                'slug' => $slug,
                'short_description' => 'This is a short description for ' . $title,
                'matrial' => $materials[array_rand($materials)],
                'image_cover' => 'sample-product.jpg', // خليه ثابت لأي صورة تجريبية عندك
                'price_before_discount' => rand(100, 999),
                'status' => 'active',
                'sub_category_id' => $subCategories[array_rand($subCategories)],
            ]);
        }
    }
}