<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            ['name' => 'Meja 1', 'status' => 'available'],
            ['name' => 'Meja 2', 'status' => 'available'],
            ['name' => 'Meja 3', 'status' => 'available'],
            ['name' => 'Meja 4', 'status' => 'available'],
            ['name' => 'Meja 5', 'status' => 'available'],
        ]);
    }
}