<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->json('locations')->nullable()->after('lat');
        });

        // Migrate existing translatable location strings into the new array format
        DB::table('artisans')->whereNotNull('location')->orderBy('id')->each(function ($row) {
            $old = json_decode($row->location, true);
            if (!is_array($old)) {
                return;
            }

            $entry = [
                'en' => $old['en'] ?? '',
                'fr' => $old['fr'] ?? '',
                'ar' => $old['ar'] ?? '',
            ];

            // Only create an entry if at least one locale has content
            if (array_filter(array_values($entry))) {
                DB::table('artisans')->where('id', $row->id)->update([
                    'locations' => json_encode([$entry]),
                ]);
            }
        });

        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->json('location')->nullable();
        });

        // Restore: take the first locations entry back into the translatable column
        DB::table('artisans')->whereNotNull('locations')->orderBy('id')->each(function ($row) {
            $locs = json_decode($row->locations, true);
            if (!is_array($locs) || empty($locs)) {
                return;
            }

            $first = $locs[0];
            DB::table('artisans')->where('id', $row->id)->update([
                'location' => json_encode([
                    'en' => $first['en'] ?? '',
                    'fr' => $first['fr'] ?? '',
                    'ar' => $first['ar'] ?? '',
                ]),
            ]);
        });

        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn('locations');
        });
    }
};
