<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CoachingTopic;

class CoachingTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            'Laravel',
            'Vue.js',
            'Inertia.js',
            'Deployment Hosting',
            'Payment Gateway',
            'WebGIS',
            'Machine Learning',
            'Data Science',
            'AI Tools for Coding',
            'Research Paper / Jurnal',
        ];

        foreach ($topics as $index => $topic) {
            CoachingTopic::firstOrCreate(
                ['slug' => Str::slug($topic)],
                [
                    'name' => $topic,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
