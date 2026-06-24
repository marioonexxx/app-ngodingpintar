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
        Schema::create('project_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->text('member_reason');
            $table->string('member_proof')->nullable();
            $table->text('vendor_response')->nullable();
            $table->string('vendor_proof')->nullable();
            $table->text('admin_decision')->nullable();
            $table->string('status', 30)->default('pending_vendor'); // pending_vendor, pending_admin, resolved_refund, resolved_release
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_complaints');
    }
};
