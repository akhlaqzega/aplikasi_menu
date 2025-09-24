<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MenuSeeder::class,
            AdminSeeder::class,
            TableSeeder::class,
        ]);
    }
}