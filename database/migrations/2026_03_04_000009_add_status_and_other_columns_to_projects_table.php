<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add status, owner, value, is_hidden to projects table if missing.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'status')) {
                $table->string('status', 50)->default('قيد التنفيذ');
            }
            if (!Schema::hasColumn('projects', 'owner')) {
                $table->string('owner')->nullable();
            }
            if (!Schema::hasColumn('projects', 'value')) {
                $table->decimal('value', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('projects', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = ['status', 'owner', 'value', 'is_hidden'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
