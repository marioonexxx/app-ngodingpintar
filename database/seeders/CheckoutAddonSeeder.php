<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CheckoutAddon;

class CheckoutAddonSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('checkout_addons')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        CheckoutAddon::create([
            'name' => 'Coaching Install Local/Hosting',
            'slug' => 'coaching-install-local-hosting',
            'description' => 'Pendampingan instalasi source code di komputer lokal atau hosting melalui Google Meet/Zoom.',
            'type' => 'paid',
            'price' => 300000,
            'is_active' => true,
        ]);

        CheckoutAddon::create([
            'name' => 'Request Custom Fitur',
            'slug' => 'request-custom-fitur',
            'description' => 'Request penambahan atau penyesuaian fitur khusus untuk source code.',
            'type' => 'custom_request',
            'price' => null,
            'is_active' => true,
        ]);
    }
}
