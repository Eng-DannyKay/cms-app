<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Client;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();

        foreach ($clients as $client) {

            Page::create([
                'client_id' => $client->id,
                'title' => 'Homepage',
                'slug' => 'home',
                'content' => json_encode([
                    'sections' => [
                        [
                            'type' => 'hero',
                            'title' => 'Welcome to ' . $client->company_name,
                            'subtitle' => 'We provide amazing services',
                            'image' => null,
                            'button_text' => 'Get Started',
                            'button_link' => '#services'
                        ],
                        [
                            'type' => 'services',
                            'title' => 'Our Services',
                            'services' => [
                                ['title' => 'Web Development', 'description' => 'Professional web development services'],
                                ['title' => 'SEO Optimization', 'description' => 'Improve your search engine ranking'],
                                ['title' => 'Content Marketing', 'description' => 'Engage your audience with great content']
                            ]
                        ]
                    ]
                ]),
                'published_content' => json_encode([
                    'sections' => [
                        [
                            'type' => 'hero',
                            'title' => 'Welcome to ' . $client->company_name,
                            'subtitle' => 'We provide amazing services',
                            'image' => null,
                            'button_text' => 'Get Started',
                            'button_link' => '#services'
                        ]
                    ]
                ]),
                'is_published' => true,
                'version' => 1
            ]);

            Page::create([
                'client_id' => $client->id,
                'title' => 'About Us',
                'slug' => 'about',
                'content' => json_encode([
                    'sections' => [
                        [
                            'type' => 'about',
                            'title' => 'About ' . $client->company_name,
                            'content' => 'We are a company dedicated to providing excellent services to our clients. With years of experience in the industry, we have helped numerous businesses achieve their goals.',
                            'image' => null
                        ]
                    ]
                ]),
                'is_published' => false,
                'version' => 1
            ]);


            Page::create([
                'client_id' => $client->id,
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => json_encode([
                    'sections' => [
                        [
                            'type' => 'contact',
                            'title' => 'Get in Touch',
                            'email' => 'info@' . strtolower(str_replace(' ', '', $client->company_name)) . '.com',
                            'phone' => '+1 (555) 123-4567',
                            'address' => '123 Business Street, City, State 12345'
                        ]
                    ]
                ]),
                'is_published' => true,
                'version' => 1
            ]);
        }
    }
}
