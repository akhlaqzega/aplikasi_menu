<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('menus')->insert([
    [
        'name' => 'Kue Latte',
        'description' => 'Espresso dengan susu steamed dan foam tipis.',
        'price' => 25000,
        'image' => 'images/menus/latte.jpg', // relative path dari public/
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Kopi Americano',
        'description' => 'Espresso shot dengan air panas.',
        'price' => 20000,
        'image' => 'images/menus/americano.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Kue Cokelat',
        'description' => 'Kue cokelat yang lembut dan kaya rasa.',
        'price' => 35000,
        'image' => 'images/menus/kue-cokelat.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Nasi Goreng',
        'description' => 'Nasi goreng spesial dengan ayam dan telur.',
        'price' => 45000,
        'image' => 'images/menus/nasi-goreng.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Spaghetti Bolognese',
        'description' => 'Spaghetti dengan saus daging bolognese.',
        'price' => 55000,
        'image' => 'images/menus/spaghetti.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

    }
}