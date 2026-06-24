<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ProductCategorySeeder::class,
            MembershipSeeder::class,
            ProductSeeder::class,
            TransactionSeeder::class,
            TestimonialSeeder::class,
            PromoCodeSeeder::class,
            CheckoutAddonSeeder::class,
            CoachingTopicSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@ngodingpintar.test')],
            [
                'name' => env('ADMIN_NAME', 'NgodingPintar Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => env('MEMBER_EMAIL', 'member@ngodingpintar.test')],
            [
                'name' => env('MEMBER_NAME', 'NgodingPintar Member'),
                'password' => Hash::make(env('MEMBER_PASSWORD', 'password')),
                'role' => User::ROLE_MEMBER,
                'email_verified_at' => now(),
            ],
        );
    }
}
