<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('comment');
            $table->longText('admin_notes')->nullable()->after('status');
            $table->string('submitted_by_name')->nullable()->after('admin_notes');
            $table->string('submitted_by_email')->nullable()->after('submitted_by_name');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'admin_notes', 'submitted_by_name', 'submitted_by_email']);
        });
    }
};
