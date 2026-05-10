<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn(['name', 'bio', 'location']);
        });

        Schema::table('artisans', function (Blueprint $table) {
            $table->json('name')->after('category_id');
            $table->json('bio')->nullable()->after('slug');
            $table->json('location')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn(['name', 'bio', 'location']);
        });

        Schema::table('artisans', function (Blueprint $table) {
            $table->string('name')->after('category_id');
            $table->text('bio')->nullable()->after('slug');
            $table->string('location')->nullable()->after('city');
        });
    }
};
