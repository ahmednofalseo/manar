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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخدمة
            $table->string('slug')->unique(); // معرف فريد
            $table->text('description')->nullable(); // وصف الخدمة
            $table->string('icon')->nullable(); // أيقونة الخدمة (أيقونة فارغة للتسمية التفصيلية)
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete(); // الفئة
            $table->foreignId('parent_id')->nullable()->constrained('services')->nullOnDelete(); // خدمة رئيسية (لخدمات بلدي الفرعية)
            $table->boolean('is_custom')->default(false); // خدمة مخصصة
            $table->boolean('has_sub_services')->default(false); // يدعم خدمات فرعية (مثل خدمات بلدي)
            $table->integer('order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true); // نشط/غير نشط
            $table->timestamps();
            
            // Indexes
            $table->index('category_id');
            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
