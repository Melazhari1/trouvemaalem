<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'content']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->json('title')->after('id');
            $table->json('excerpt')->nullable()->after('slug');
            $table->json('content')->after('excerpt');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'content']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('excerpt')->nullable()->after('slug');
            $table->longText('content')->after('excerpt');
        });
    }
};
