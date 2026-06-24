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

        Schema::dropIfExists('coaching_bookings');
        Schema::dropIfExists('coaching_topics');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not providing a down method because the user specifically asked to remove it permanently.
    }
};
