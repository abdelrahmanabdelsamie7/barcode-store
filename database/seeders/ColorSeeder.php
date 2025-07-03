<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ColorSeeder extends Seeder
{
    public function run()
    {
        $colors = [
            ['name' => 'White',     'hex_code' => '#FFFFFF'],
            ['name' => 'Black',     'hex_code' => '#000000'],
            ['name' => 'Red',       'hex_code' => '#FF0000'],
            ['name' => 'Blue',      'hex_code' => '#0000FF'],
            ['name' => 'Green',     'hex_code' => '#008000'],
            ['name' => 'Yellow',    'hex_code' => '#FFFF00'],
            ['name' => 'Gray',      'hex_code' => '#808080'],
            ['name' => 'Brown',     'hex_code' => '#A52A2A'],
            ['name' => 'Orange',    'hex_code' => '#FFA500'],
            ['name' => 'Purple',    'hex_code' => '#800080'],
        ];
        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'id' => (string) Str::uuid(),
                'name' => $color['name'],
                'hex_code' => $color['hex_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
