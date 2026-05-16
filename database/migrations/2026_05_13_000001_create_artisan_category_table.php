<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_category', function (Blueprint $table) {
            $table->foreignId('artisan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['artisan_id', 'category_id']);
        });

        // Migrate existing category_id values into the pivot table
        DB::table('artisans')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->orderBy('id')
            ->each(function ($artisan) {
                DB::table('artisan_category')->insertOrIgnore([
                    'artisan_id'  => $artisan->id,
                    'category_id' => $artisan->category_id,
                ]);
            });

        Schema::table('artisans', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        });

        // Restore: take the first category from the pivot per artisan
        DB::table('artisan_category')
            ->orderBy('artisan_id')
            ->orderBy('category_id')
            ->each(function ($row) {
                DB::table('artisans')
                    ->where('id', $row->artisan_id)
                    ->whereNull('category_id')
                    ->update(['category_id' => $row->category_id]);
            });

        Schema::dropIfExists('artisan_category');
    }
};
