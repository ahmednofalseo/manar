<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make province_id nullable - the app doesn't use provinces.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('cities') && Schema::hasColumn('cities', 'province_id')) {
            DB::statement('ALTER TABLE cities MODIFY COLUMN province_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('cities') && Schema::hasColumn('cities', 'province_id')) {
            DB::statement('ALTER TABLE cities MODIFY COLUMN province_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
