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
        if (Schema::hasTable('workflow_steps')) {
            return;
        }
        
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')->constrained('workflow_templates')->onDelete('cascade'); // القالب
            $table->string('name'); // اسم الخطوة
            $table->text('description')->nullable(); // وصف الخطوة
            $table->integer('order')->default(0); // ترتيب الخطوة
            $table->enum('department', [
                'معماري',
                'إنشائي',
                'كهربائي',
                'ميكانيكي',
                'مساحي',
                'دفاع_مدني',
                'بلدي',
                'أخرى'
            ])->default('معماري'); // القسم المسؤول
            $table->integer('default_duration_days')->default(7); // المدة الافتراضية بالأيام (SLA)
            $table->json('expected_outputs')->nullable(); // المخرجات المتوقعة (Files/Approvals/Tasks)
            $table->json('dependencies')->nullable(); // خطوات سابقة مطلوبة (للترتيب)
            $table->boolean('is_parallel')->default(false); // يمكن تنفيذها بالتوازي
            $table->boolean('is_required')->default(true); // خطوة مطلوبة
            $table->timestamps();
            
            // Indexes
            $table->index('workflow_template_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
