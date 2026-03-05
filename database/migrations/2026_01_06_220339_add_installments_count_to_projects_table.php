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
        if (Schema::hasColumn('projects', 'installments_count')) {
            return;
        }
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'value')) {
                $table->integer('installments_count')->default(1)->after('value');
            } else {
                $table->integer('installments_count')->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('installments_count');
        });
    }
};
