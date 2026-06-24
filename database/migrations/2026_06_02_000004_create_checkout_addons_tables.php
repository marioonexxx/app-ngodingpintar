<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['paid', 'custom_request']);
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('addon_total', 15, 2)->default(0)->after('discount_total');
        });

        Schema::create('transaction_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('checkout_addon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type');
            $table->decimal('price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('custom_feature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('description');
            $table->enum('status', ['pending_review', 'quoted', 'approved', 'rejected', 'completed'])->default('pending_review');
            $table->decimal('quoted_amount', 15, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('vendor_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_feature_requests');
        Schema::dropIfExists('transaction_addons');

        if (Schema::hasColumn('transactions', 'addon_total')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('addon_total');
            });
        }

        Schema::dropIfExists('checkout_addons');
    }
};
