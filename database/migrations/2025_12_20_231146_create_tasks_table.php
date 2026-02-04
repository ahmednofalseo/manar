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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            
            // العلاقات الأساسية
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_stage_id')->nullable()->constrained('project_stages')->nullOnDelete(); // مرحلة المشروع
            $table->foreignId('assignee_id')->constrained('users')->onDelete('restrict'); // الموظف المسند إليه
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // من أنشأ المهمة
            
            // بيانات المهمة
            $table->string('title'); // عنوان المهمة
            $table->text('description')->nullable(); // وصف المهمة
            $table->text('manager_notes')->nullable(); // ملاحظات مدير المشروع
            
            // الحالة
            $table->enum('status', ['new', 'in_progress', 'done', 'rejected'])->default('new');
            $table->text('rejection_reason')->nullable(); // سبب الرفض (مطلوب عند rejected)
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete(); // من رفض المهمة
            $table->timestamp('rejected_at')->nullable(); // تاريخ الرفض
            
            // الأولوية
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            
            // التواريخ
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable(); // تاريخ الإنجاز
            
            // التقدم
            $table->integer('progress')->default(0); // نسبة الإنجاز 0-100
            $table->text('completion_notes')->nullable(); // ملاحظات الإنجاز
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['assignee_id', 'status']);
            $table->index(['project_stage_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
