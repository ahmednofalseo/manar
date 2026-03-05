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
        if (Schema::hasColumn('projects', 'service_id')) {
            return;
        }
        Schema::table('projects', function (Blueprint $table) {
            $column = $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            if (Schema::hasColumn('projects', 'type')) {
                $column->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
