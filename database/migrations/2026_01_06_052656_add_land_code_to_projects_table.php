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
        Schema::table('projects', function (Blueprint $table) {
            // إضافة حقل land_code بعد land_number
            $table->string('land_code')->nullable()->after('land_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('land_code');
        });
    }
};
