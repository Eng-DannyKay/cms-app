<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $themes = Theme::all();

        foreach ($clients as $client) {
            $randomTheme = $themes->random();
            $client->themes()->attach($randomTheme->id, [
                'customizations' => json_encode([
                    'primary' => $this->adjustColor($randomTheme->colors['primary']),
                    'button_radius' => '8px'
                ])
            ]);
        }
    }

    private function adjustColor(string $color): string
    {

        return '#' . dechex(hexdec(substr($color, 1)) + 0x111111);
    }
}
