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
        if (Schema::hasTable('project_workflows')) {
            return;
        }
        
        Schema::create('project_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // المشروع
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict'); // الخدمة المختارة
            $table->foreignId('workflow_template_id')->nullable()->constrained('workflow_templates')->nullOnDelete(); // القالب المستخدم (يمكن تعديله)
            $table->string('name'); // اسم المسار
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active'); // حالة المسار
            $table->boolean('is_parallel')->default(false); // مسار متوازي
            $table->integer('parent_workflow_id')->nullable(); // المسار الرئيسي (للمسارات المتوازية)
            $table->date('start_date')->nullable(); // تاريخ البدء
            $table->date('expected_end_date')->nullable(); // تاريخ الانتهاء المتوقع
            $table->date('actual_end_date')->nullable(); // تاريخ الانتهاء الفعلي
            $table->integer('progress')->default(0); // نسبة التقدم 0-100
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
            
            // Indexes
            $table->index('project_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('parent_workflow_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_workflows');
    }
};
