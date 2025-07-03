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
            '0ae082d2-aa21-45c5-8889-3a87bb6177c5', // Round T-shirts
            '180d9a7f-b0b9-450e-94aa-c832909365d1', // Polo T-shirt
            '8f6e07a6-d20e-408d-ada3-c2aed73eba9e', // Sets
        ];

        for ($i = 1; $i <= 15; $i++) {
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