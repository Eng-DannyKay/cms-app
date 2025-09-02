<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ThemeSeeder::class,
            ClientSeeder::class,
            PageSeeder::class,
        ]);
    }
}
