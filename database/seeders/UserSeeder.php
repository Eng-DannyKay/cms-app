<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@cms.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create client users
        $client1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);

        $client2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);

        // Create client profiles
        Client::create([
            'user_id' => $client1->id,
            'company_name' => 'Doe Enterprises',
            'slug' => 'doe-enterprises',
            'website_url' => 'https://doe-enterprises.com'
        ]);

        Client::create([
            'user_id' => $client2->id,
            'company_name' => 'Smith Solutions',
            'slug' => 'smith-solutions',
            'website_url' => 'https://smith-solutions.com'
        ]);

        // Create additional test clients
        User::factory(5)->create(['role' => 'client'])->each(function ($user) {
            Client::create([
                'user_id' => $user->id,
                'company_name' => fake()->company(),
                'slug' => \Illuminate\Support\Str::slug(fake()->company()),
                'website_url' => fake()->url()
            ]);
        });
    }
}
