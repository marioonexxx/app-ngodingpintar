<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pindahkan data dari vendors ke partner_profiles
        $vendors = DB::table('vendors')->get();
        foreach ($vendors as $vendor) {
            DB::table('partner_profiles')->updateOrInsert(
                ['user_id' => $vendor->user_id],
                [
                    'brand_name' => $vendor->store_name,
                    'bio' => $vendor->developer_description,
                    'whatsapp' => $vendor->whatsapp,
                    'github_url' => $vendor->github_url,
                    'website_url' => $vendor->website_url,
                    'portfolio_url' => $vendor->portfolio_url,
                    'bank_name' => $vendor->bank_name,
                    'bank_account_name' => $vendor->bank_account_name,
                    'bank_account_number' => $vendor->bank_account_number,
                    'vendor_status' => $vendor->status,
                    'vendor_rejection_reason' => $vendor->rejection_reason,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at,
                ]
            );
        }

        // 2. Pindahkan data dari mentors ke partner_profiles
        $mentors = DB::table('mentors')->get();
        foreach ($mentors as $mentor) {
            $existing = DB::table('partner_profiles')->where('user_id', $mentor->user_id)->first();
            
            $data = [
                'whatsapp' => $mentor->whatsapp ?? ($existing->whatsapp ?? null),
                'profile_picture' => $mentor->profile_picture,
                'linkedin_url' => $mentor->linkedin_url,
                'github_url' => $mentor->github_url ?? ($existing->github_url ?? null),
                'website_url' => $mentor->website_url ?? ($existing->website_url ?? null),
                'expertise_area' => $mentor->expertise_area,
                'experience_years' => $mentor->experience_years,
                'certifications' => $mentor->certifications,
                'bank_name' => $mentor->bank_name ?? ($existing->bank_name ?? null),
                'bank_account_name' => $mentor->bank_account_name ?? ($existing->bank_account_name ?? null),
                'bank_account_number' => $mentor->bank_account_number ?? ($existing->bank_account_number ?? null),
                'mentor_status' => $mentor->status,
                'mentor_rejection_reason' => $mentor->rejection_reason,
                'updated_at' => now(),
            ];

            // Hanya update nama brand dan bio jika sebelumnya kosong
            if (!$existing || empty($existing->brand_name)) {
                $data['brand_name'] = $mentor->name;
            }
            if (!$existing || empty($existing->bio)) {
                $data['bio'] = $mentor->bio;
            }

            DB::table('partner_profiles')->updateOrInsert(
                ['user_id' => $mentor->user_id],
                $data
            );
        }

        // 3. Update User roles
        DB::table('users')
            ->whereIn('role', ['vendor', 'mentor', 'vendor_mentor'])
            ->update(['role' => 'partner']);

        // 4. Drop tabel lama
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('mentors');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk down method, setidaknya kembalikan role
        DB::table('users')
            ->where('role', 'partner')
            ->update(['role' => 'member']); // Fallback yang aman

        // Tidak mengembalikan skema/tabel vendors & mentors secara otomatis 
        // karena butuh schema builder lengkap.
    }
};
