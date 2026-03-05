<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change projects.type from enum to varchar to accept any value from project_types table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('projects') || !Schema::hasColumn('projects', 'type')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE projects MODIFY COLUMN type VARCHAR(255) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('projects') || !Schema::hasColumn('projects', 'type')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN type ENUM(
                'تصميم',
                'تصميم وإشراف',
                'إشراف',
                'تقرير فني',
                'تقرير دفاع مدني',
                'تصميم دفاع مدني',
                'تعديلات',
                'استشارات'
            ) NOT NULL");
        }
    }
};
