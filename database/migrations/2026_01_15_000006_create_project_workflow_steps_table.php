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
        Schema::create('project_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_workflow_id')->constrained('project_workflows')->onDelete('cascade'); // مسار المشروع
            $table->foreignId('workflow_step_id')->nullable()->constrained('workflow_steps')->nullOnDelete(); // الخطوة من القالب (يمكن تعديلها)
            $table->string('name'); // اسم الخطوة (يمكن تعديله)
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
            $table->integer('duration_days')->default(7); // المدة بالأيام
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped', 'blocked'])->default('pending'); // حالة الخطوة
            $table->date('start_date')->nullable(); // تاريخ البدء الفعلي
            $table->date('expected_end_date')->nullable(); // تاريخ الانتهاء المتوقع
            $table->date('actual_end_date')->nullable(); // تاريخ الانتهاء الفعلي
            $table->integer('delay_days')->default(0); // أيام التأخير
            $table->json('expected_outputs')->nullable(); // المخرجات المتوقعة
            $table->json('actual_outputs')->nullable(); // المخرجات الفعلية
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // المسؤول عن الخطوة
            $table->text('notes')->nullable(); // ملاحظات
            $table->boolean('is_custom')->default(false); // خطوة مخصصة (أضيفت يدوياً)
            $table->timestamps();
            
            // Indexes
            $table->index('project_workflow_id');
            $table->index('status');
            $table->index('assigned_to');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_workflow_steps');
    }
};
