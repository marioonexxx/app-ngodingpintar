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
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type')->default('digital_product')->after('id');
            $table->foreignId('coaching_topic_id')->nullable()->constrained('coaching_topics')->nullOnDelete()->after('membership_id');
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete()->after('coaching_topic_id');
            $table->integer('session_duration_minutes')->nullable()->after('status');
            $table->string('meeting_platform')->nullable()->after('session_duration_minutes');
            $table->text('meeting_note')->nullable()->after('meeting_platform');
        });

        // Make product_category_id nullable as coaching products might not use it
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable(false)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['coaching_topic_id']);
            $table->dropForeign(['mentor_id']);
            $table->dropColumn([
                'product_type',
                'coaching_topic_id',
                'mentor_id',
                'session_duration_minutes',
                'meeting_platform',
                'meeting_note',
            ]);
        });
    }
};
