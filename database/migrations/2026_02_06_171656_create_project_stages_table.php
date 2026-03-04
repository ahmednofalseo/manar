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
        if (Schema::hasTable('stage_settings')) {
            return;
        }
        
        Schema::create('stage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المرحلة
            $table->string('name_en')->nullable(); // الاسم بالإنجليزية
            $table->text('description')->nullable(); // الوصف
            $table->string('icon')->nullable(); // أيقونة المرحلة
            $table->string('color')->nullable(); // لون المرحلة
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
        Schema::dropIfExists('stage_settings');
    }
};
