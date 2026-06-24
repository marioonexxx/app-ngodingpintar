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
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('category', 50)->default('new_app'); // new_app, revision
            $table->text('description');
            $table->decimal('budget', 12, 2);
            $table->integer('deadline_days')->default(0);
            $table->string('status', 50)->default('pending_admin'); // pending_admin, rejected, waiting_payment, open, in_progress, completed, complained, refund_pending, refunded, cancelled
            $table->text('rejection_reason')->nullable();
            $table->string('github_link')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
