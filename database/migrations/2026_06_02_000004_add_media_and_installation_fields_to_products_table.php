<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'screenshots')) {
                $table->json('screenshots')->nullable()->after('thumbnail');
            }

            if (! Schema::hasColumn('products', 'installation_guide')) {
                $table->string('installation_guide')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (Schema::hasColumn('products', 'installation_guide')) {
                $table->dropColumn('installation_guide');
            }

            if (Schema::hasColumn('products', 'screenshots')) {
                $table->dropColumn('screenshots');
            }
        });
    }
};
