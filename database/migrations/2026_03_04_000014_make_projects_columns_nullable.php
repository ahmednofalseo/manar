<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make legacy project columns nullable for CRM compatibility.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || !Schema::hasTable('projects')) {
            return;
        }

        $bigintCols = ['types_project_id', 'user_id', 'country_id', 'province_id', 'city_id'];
        $tinyintCols = ['industrial', 'agricultural', 'commercial', 'technical_means', 'material_means',
            'human_resources', 'media', 'financial_resources', 'publications', 'government_approvals',
            'price', 'delivery_service', 'mobile_app', 'quality', 'saving_speed',
            'encouragement_incentives', 'need_investor'];
        $enumCols = ['target_group' => "ENUM('all_people','adults','children','students','women','men') NULL"];

        foreach ($bigintCols as $col) {
            if (Schema::hasColumn('projects', $col)) {
                DB::statement("ALTER TABLE projects MODIFY COLUMN `{$col}` BIGINT UNSIGNED NULL");
            }
        }
        foreach ($tinyintCols as $col) {
            if (Schema::hasColumn('projects', $col)) {
                DB::statement("ALTER TABLE projects MODIFY COLUMN `{$col}` TINYINT(1) NULL");
            }
        }
        foreach ($enumCols as $col => $def) {
            if (Schema::hasColumn('projects', $col)) {
                DB::statement("ALTER TABLE projects MODIFY COLUMN `{$col}` {$def}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing
    }
};
