<?php
namespace Database\Seeders;
use App\Models\Size;
use Illuminate\Database\Seeder;
class SizeSeeder extends Seeder
{
    public function run()
    {
        $sizes = [
            ['name' => 'S', 'order' => 1],
            ['name' => 'M', 'order' => 2],
            ['name' => 'L', 'order' => 3],
            ['name' => 'XL', 'order' => 4],
            ['name' => '2XL', 'order' => 5],
            ['name' => '3XL', 'order' => 6],
            ['name' => '4XL', 'order' => 7],
            ['name' => '5XL', 'order' => 8],
            ['name' => '6XL', 'order' => 9],
            ['name' => '7XL', 'order' => 10],
            ['name' => '8XL', 'order' => 11],
            ['name' => '9XL', 'order' => 12],
            ['name' => '10XL', 'order' =>13],
            // بطونات
            ['name' => '29', 'order' => 101],
            ['name' => '30', 'order' => 102],
            ['name' => '32', 'order' => 103],
            ['name' => '34', 'order' => 104],
            ['name' => '36', 'order' => 105],
            ['name' => '38', 'order' => 106],
            ['name' => '40', 'order' => 107],
            ['name' => '42', 'order' => 108],
            ['name' => '44', 'order' => 109],
            ['name' => '46', 'order' => 110],
            ['name' => '48', 'order' => 111],
            ['name' => '50', 'order' => 112],
            // أحذية
            ['name' => '41', 'order' => 201],
            ['name' => '42', 'order' => 202],
            ['name' => '43', 'order' => 203],
            ['name' => '44', 'order' => 204],
            ['name' => '45', 'order' => 205],
            ['name' => '46', 'order' => 206],
        ];
        foreach ($sizes as $size) {
            Size::updateOrCreate(
                ['name' => $size['name']],
                ['order' => $size['order']]
            );
        }
    }
}
