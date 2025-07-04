<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\{AdminSeeder,SizeSeeder,ProductSeeder,ColorSeeder};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
    $this->call([
            AdminSeeder::class,
            SizeSeeder::class ,
            // ProductSeeder::class,
            ColorSeeder::class,
        ]);
    }
}
