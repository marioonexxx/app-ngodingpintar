<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update products table: digital_product -> source_code
        DB::table('products')
            ->where('product_type', 'digital_product')
            ->update(['product_type' => 'source_code']);

        // Update product_categories table default and existing values
        DB::table('product_categories')
            ->where('product_type', 'digital_product')
            ->update(['product_type' => 'source_code']);
    }

    public function down(): void
    {
        DB::table('products')
            ->where('product_type', 'source_code')
            ->update(['product_type' => 'digital_product']);

        DB::table('product_categories')
            ->where('product_type', 'source_code')
            ->update(['product_type' => 'digital_product']);
    }
};
