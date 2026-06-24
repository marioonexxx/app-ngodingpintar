<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Profil Identitas Umum
            $table->string('brand_name')->nullable();
            $table->text('bio')->nullable();
            
            // Kontak & Sosial Media
            $table->string('whatsapp')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('portfolio_url')->nullable();
            
            // Keahlian Khusus Mentor
            $table->string('expertise_area')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('certifications')->nullable();
            
            // Informasi Bank
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            
            // Status Validasi Vendor
            $table->string('vendor_status')->nullable();
            $table->text('vendor_rejection_reason')->nullable();
            
            // Status Validasi Mentor
            $table->string('mentor_status')->nullable();
            $table->text('mentor_rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_profiles');
    }
};
