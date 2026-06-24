<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Mentor;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Categories
        $sourceCodeCategory = ProductCategory::firstOrCreate(
            ['slug' => 'source-code-dummy'],
            ['name' => 'Source Code Dummy', 'is_active' => true, 'product_type' => 'source_code']
        );
        $classCategory = ProductCategory::firstOrCreate(
            ['slug' => 'class-dummy'],
            ['name' => 'Class Dummy', 'is_active' => true, 'product_type' => 'class'] 
        );

        // 1. Members (20)
        for ($i = 1; $i <= 20; $i++) {
            User::updateOrCreate(
                ['email' => "member{$i}+dummy@gmail.com"],
                [
                    'name' => "Member {$i} Dummy",
                    'password' => $password,
                    'role' => User::ROLE_MEMBER,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 2. Vendors (5)
        $vendorProducts = [
            ["Dummy Web POS", "Dummy Web Kasir"],
            ["Dummy App Android Ojek", "Dummy Web Profil"],
            ["Dummy SIAKAD Web", "Dummy E-Learning"],
            ["Dummy ERP Sistem", "Dummy CRM"],
            ["Dummy IoT Dashboard", "Dummy Inventory"],
        ];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::updateOrCreate(
                ['email' => "vendor{$i}+dummy@gmail.com"],
                [
                    'name' => "Vendor {$i} Dummy",
                    'password' => $password,
                    'role' => User::ROLE_VENDOR,
                    'email_verified_at' => now(),
                ]
            );

            $vendor = Vendor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'store_name' => "Vendor Code " . chr(64 + $i), // A, B, C...
                    'status' => 'approved',
                ]
            );

            foreach ($vendorProducts[$i-1] as $prodName) {
                $product = Product::updateOrCreate(
                    ['slug' => Str::slug($prodName)],
                    [
                        'name' => $prodName,
                        'price' => rand(50000, 500000),
                        'status' => Product::STATUS_ACTIVE,
                        'product_type' => 'source_code',
                        'vendor_id' => $user->id,
                    ]
                );
                $product->categories()->sync([$sourceCodeCategory->id]);
            }
        }

        // 3. Mentors (5)
        $mentorExpertises = ['Web Dev', 'Mobile Dev', 'Data Science', 'UI/UX', 'Cyber Security'];
        $mentorProducts = [
            ["Dummy Kelas React JS", "Dummy Kelas Vue JS"],
            ["Dummy Kelas Flutter", "Dummy Kelas Kotlin"],
            ["Dummy Kelas Python ML", "Dummy Kelas Data Analyst"],
            ["Dummy Kelas Figma Basic", "Dummy Kelas UI Advanced"],
            ["Dummy Kelas Ethical Hacking", "Dummy Kelas Network Sec"],
        ];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::updateOrCreate(
                ['email' => "mentor{$i}+dummy@gmail.com"],
                [
                    'name' => "Mentor {$i} Dummy",
                    'password' => $password,
                    'role' => User::ROLE_MENTOR,
                    'email_verified_at' => now(),
                ]
            );

            $mentor = Mentor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => "Mentor {$i} Dummy",
                    'expertise_area' => $mentorExpertises[$i-1],
                    'status' => 'approved',
                ]
            );

            foreach ($mentorProducts[$i-1] as $prodName) {
                $product = Product::updateOrCreate(
                    ['slug' => Str::slug($prodName)],
                    [
                        'name' => $prodName,
                        'price' => rand(100000, 1000000),
                        'status' => Product::STATUS_ACTIVE,
                        'product_type' => 'class',
                        'mentor_id' => $user->id,
                    ]
                );
                $product->categories()->sync([$classCategory->id]);
                
                $product->classBatches()->updateOrCreate(
                    ['batch_number' => 1],
                    ['status' => 'open', 'start_date' => now()->addDays(10), 'end_date' => now()->addDays(40)]
                );
            }
        }

        // 4. Dual Partners (5)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::updateOrCreate(
                ['email' => "dual{$i}+dummy@gmail.com"],
                [
                    'name' => "Dual Partner {$i} Dummy",
                    'password' => $password,
                    'role' => User::ROLE_PARTNER,
                    'email_verified_at' => now(),
                ]
            );

            $vendor = Vendor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'store_name' => "Dual Vendor {$i}",
                    'status' => 'approved',
                ]
            );

            $mentor = Mentor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => "Dual Mentor {$i}",
                    'expertise_area' => 'Fullstack Dev',
                    'status' => 'approved',
                ]
            );

            // 1 Source Code
            $prodCodeName = "Dummy Code Template {$i}";
            $productCode = Product::updateOrCreate(
                ['slug' => Str::slug($prodCodeName)],
                [
                    'name' => $prodCodeName,
                    'price' => rand(50000, 500000),
                    'status' => Product::STATUS_ACTIVE,
                    'product_type' => 'source_code',
                    'vendor_id' => $user->id,
                ]
            );
            $productCode->categories()->sync([$sourceCodeCategory->id]);

            // 1 Class
            $prodClassName = "Dummy Class Master {$i}";
            $productClass = Product::updateOrCreate(
                ['slug' => Str::slug($prodClassName)],
                [
                    'name' => $prodClassName,
                    'price' => rand(100000, 1000000),
                    'status' => Product::STATUS_ACTIVE,
                    'product_type' => 'class',
                    'mentor_id' => $user->id,
                ]
            );
            $productClass->categories()->sync([$classCategory->id]);
            $productClass->classBatches()->updateOrCreate(
                ['batch_number' => 1],
                ['status' => 'open', 'start_date' => now()->addDays(10), 'end_date' => now()->addDays(40)]
            );
        }
    }
}
