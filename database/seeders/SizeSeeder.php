<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Size;
class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['S','M','L','XL','2XL','3XL','4XL','5XL','6XL','7XL','8XL','9XL','10XL'];
        foreach ($sizes as $index => $sizeName) {
            Size::updateOrCreate(
                ['name' => $sizeName],
                [
                    'id' => Str::uuid(),
                    'order' => $index,
                ]
            );
        }
    }
}