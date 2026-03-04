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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المدينة
            $table->string('name_en')->nullable(); // الاسم بالإنجليزية
            $table->string('code')->nullable()->unique(); // كود المدينة
            $table->boolean('is_active')->default(true); // نشط/غير نشط
            $table->integer('order')->default(0); // الترتيب
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
