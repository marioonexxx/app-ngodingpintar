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
        Schema::create('product_product_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate existing data
        $products = DB::table('products')->whereNotNull('product_category_id')->get();
        foreach ($products as $product) {
            DB::table('product_product_category')->insert([
                'product_id' => $product->id,
                'product_category_id' => $product->product_category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop the old column
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropColumn('product_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
        });

        // Revert data
        $pivots = DB::table('product_product_category')->get();
        foreach ($pivots as $pivot) {
            DB::table('products')
                ->where('id', $pivot->product_id)
                ->whereNull('product_category_id')
                ->update(['product_category_id' => $pivot->product_category_id]);
        }

        Schema::dropIfExists('product_product_category');
    }
};
