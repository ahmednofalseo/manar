<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     * 
     * فصل رقم الأرض عن كود الأرض:
     * - land_number: رقم الأرض (موجود بالفعل)
     * - land_code: كود الأرض (جديد)
     */
    public function up(): void
    {
        if (Schema::hasColumn('projects', 'land_code')) {
            return;
        }
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'land_number')) {
                $table->string('land_code')->nullable()->after('land_number');
            } else {
                $table->string('land_code')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projects', 'land_code')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('land_code');
            });
        }
    }
};
