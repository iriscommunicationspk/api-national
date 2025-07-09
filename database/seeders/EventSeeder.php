<?php

namespace Database\Seeders;

use App\Models\EventsModel;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'name' => 'Sachet Focus Group Discussion',
                'description' => 'Focus group discussion for sachet products in Karachi',
                'date' => '2025-01-15',
                'link' => 'https://meet.google.com/abc-defg-hij',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consumer Profiling Survey',
                'description' => 'Consumer profiling and feedback collection for recipe mixes',
                'date' => '2025-01-20',
                'link' => 'https://forms.google.com/survey123',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product Tasting Session',
                'description' => 'Product tasting session for new mayo flavors',
                'date' => '2025-01-25',
                'link' => 'https://zoom.us/j/123456789',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Market Research Interview',
                'description' => 'One-on-one interviews for spices market research',
                'date' => '2025-02-01',
                'link' => 'https://calendly.com/research-interview',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($events as $event) {
            EventsModel::firstOrCreate(
                ['name' => $event['name'], 'date' => $event['date']],
                $event
            );
        }
    }
}
