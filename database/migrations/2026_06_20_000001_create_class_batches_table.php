<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('batch_number');
            $table->string('status')->default('open')->index();
            $table->string('schedule_type')->default('fixed');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('recurring_days')->nullable();
            $table->string('platform')->nullable();
            $table->string('meeting_link')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'batch_number']);
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->foreignId('class_batch_id')->nullable()->after('product_id')
                ->constrained('class_batches')->nullOnDelete();
        });

        Schema::table('transaction_items', function (Blueprint $table): void {
            $table->foreignId('class_batch_id')->nullable()->after('product_id')
                ->constrained('class_batches')->nullOnDelete();
        });

        $batchNumbers = [];
        $firstBatchIds = [];

        foreach (DB::table('class_products')->orderBy('id')->get() as $legacy) {
            $batchNumber = ($batchNumbers[$legacy->product_id] ?? 0) + 1;
            $batchNumbers[$legacy->product_id] = $batchNumber;

            $batchId = DB::table('class_batches')->insertGetId([
                'product_id' => $legacy->product_id,
                'batch_number' => $batchNumber,
                'status' => 'open',
                'schedule_type' => $legacy->schedule_type,
                'start_date' => $legacy->start_date,
                'end_date' => $legacy->end_date,
                'start_time' => $legacy->start_time,
                'end_time' => $legacy->end_time,
                'recurring_days' => $legacy->recurring_days,
                'platform' => $legacy->platform,
                'meeting_link' => $legacy->meeting_link,
                'max_participants' => $legacy->max_participants,
                'created_at' => $legacy->created_at,
                'updated_at' => $legacy->updated_at,
            ]);

            $firstBatchIds[$legacy->product_id] ??= $batchId;
        }

        foreach ($firstBatchIds as $productId => $batchId) {
            DB::table('cart_items')->where('product_id', $productId)->update(['class_batch_id' => $batchId]);
            DB::table('transaction_items')->where('product_id', $productId)->update(['class_batch_id' => $batchId]);
        }
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('class_batch_id');
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('class_batch_id');
        });

        Schema::dropIfExists('class_batches');
    }
};
