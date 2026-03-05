<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'display_name')) {
                $column = $table->string('display_name')->nullable();
                if (Schema::hasColumn('permissions', 'name')) {
                    $column->after('name');
                }
            }
            if (!Schema::hasColumn('permissions', 'group')) {
                $column = $table->string('group')->nullable();
                if (Schema::hasColumn('permissions', 'display_name')) {
                    $column->after('display_name');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'group']);
        });
    }
};
