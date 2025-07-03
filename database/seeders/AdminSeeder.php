<?php
namespace Database\Seeders;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'id' => Str::uuid(),
            'name' => 'Abdelrahman Abdelsamie',
            'email' => 'abdelrahman@barcodestore.shop',
            'password' => Hash::make('barcodestore'),
            'is_super_admin' => true,
        ]);
    }
}