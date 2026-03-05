<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add progress column to projects table if missing.
     */
    public function up(): void
    {
        if (Schema::hasColumn('projects', 'progress')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'status')) {
                $table->integer('progress')->default(0)->after('status');
            } else {
                $table->integer('progress')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projects', 'progress')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('progress');
            });
        }
    }
};
