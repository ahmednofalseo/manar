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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المشروع
            $table->string('project_number')->unique()->nullable(); // رقم المشروع (توليد تلقائي)
            $table->enum('type', [
                'تصميم',
                'تصميم وإشراف',
                'إشراف',
                'تقرير فني',
                'تقرير دفاع مدني',
                'تصميم دفاع مدني',
                'تعديلات',
                'استشارات'
            ]); // نوع المشروع
            $table->string('city'); // المدينة
            $table->string('district')->nullable(); // الحي
            $table->string('owner'); // المالك
            $table->decimal('value', 15, 2)->default(0); // قيمة المشروع
            $table->string('contract_number')->nullable(); // رقم العقد
            $table->string('contract_file')->nullable(); // ملف العقد
            $table->string('land_number')->nullable(); // رقم/كود الأرض
            $table->string('plan_file')->nullable(); // المخطط
            $table->string('baladi_request_number')->nullable(); // رقم طلب منصة بلدي
            
            // المراحل (JSON)
            $table->json('stages')->nullable(); // ['معماري', 'إنشائي', ...]
            
            // الحالة والتقدم
            $table->enum('status', ['قيد التنفيذ', 'مكتمل', 'متوقف', 'ملغي'])->default('قيد التنفيذ');
            $table->integer('progress')->default(0); // نسبة الإنجاز 0-100
            
            // المرحلة الحالية
            $table->string('current_stage')->nullable(); // المرحلة الحالية من المراحل السبعة
            
            // إسناد الفريق
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete(); // مدير المشروع
            $table->json('team_members')->nullable(); // [user_id, user_id, ...]
            
            // ملاحظات
            $table->text('internal_notes')->nullable(); // ملاحظات داخلية
            
            // التواريخ
            $table->date('start_date')->nullable(); // تاريخ البدء
            $table->date('end_date')->nullable(); // تاريخ الانتهاء المتوقع
            
            $table->timestamps();
            $table->softDeletes();
        });

        // جدول المراحل التفصيلية
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('stage_name'); // اسم المرحلة
            $table->enum('status', ['جديد', 'جارٍ', 'مكتمل'])->default('جديد');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('progress')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // جدول المرفقات
        Schema::create('project_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // اسم الملف
            $table->string('file_path'); // مسار الملف
            $table->string('file_type')->nullable(); // نوع الملف
            $table->string('file_size')->nullable(); // حجم الملف
            $table->string('category')->nullable(); // نوع المرفق (عقد، رسومات، مستندات أولية)
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // جدول الأطراف الثالثة
        Schema::create('project_third_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // اسم الجهة/الطرف الثالث
            $table->date('date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_third_parties');
        Schema::dropIfExists('project_attachments');
        Schema::dropIfExists('project_stages');
        Schema::dropIfExists('projects');
    }
};
