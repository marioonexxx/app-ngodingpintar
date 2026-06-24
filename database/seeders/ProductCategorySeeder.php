<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'name' => 'Website & Landing Page',
                'slug' => 'website-landing-page',
                'description' => 'Website profesional untuk sekolah, desa, gereja, UMKM, organisasi, dan perusahaan.',
            ],
            [
                'name' => 'Web Application',
                'slug' => 'web-application',
                'description' => 'Aplikasi berbasis web untuk kebutuhan bisnis, pendidikan, pemerintahan, dan organisasi.',
            ],
            [
                'name' => 'Web SaaS',
                'slug' => 'web-saas',
                'description' => 'Solusi Software as a Service berbasis web yang siap digunakan secara online dengan fitur modern dan scalable.',
            ],
            [
                'name' => 'Web Machine Learning',
                'slug' => 'web-machine-learning',
                'description' => 'Aplikasi berbasis Artificial Intelligence dan Machine Learning untuk prediksi, klasifikasi, rekomendasi, dan analisis data.',
            ],
            [
                'name' => 'Web Data Science',
                'slug' => 'web-data-science',
                'description' => 'Dashboard analitik, visualisasi data, business intelligence, dan sistem pendukung keputusan berbasis data.',
            ],
            [
                'name' => 'Source Code Premium',
                'slug' => 'source-code-premium',
                'description' => 'Kumpulan source code premium berbasis Laravel, Vue, Inertia, dan teknologi web modern yang siap dikembangkan.',
            ],
            [
                'name' => 'Add-ons & Services',
                'slug' => 'addons-services',
                'description' => 'Layanan tambahan seperti instalasi hosting, setup VPS, deployment, custom development, konsultasi, dan coaching 1-on-1.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
