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
        Schema::create('task_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // من قام بالإجراء
            
            // نوع الإجراء
            $table->enum('action_type', [
                'status_change', // تغيير الحالة
                'rejection',     // رفض
                'comment',       // تعليق/ملاحظة
                'attachment',    // رفع مرفق
                'reopen',        // إعادة فتح
                'assignment'     // إسناد
            ]);
            
            // البيانات
            $table->string('old_value')->nullable(); // القيمة القديمة (مثلاً: الحالة القديمة)
            $table->string('new_value')->nullable(); // القيمة الجديدة (مثلاً: الحالة الجديدة)
            $table->text('notes')->nullable(); // الملاحظات/التفاصيل
            $table->text('reason')->nullable(); // السبب (للرفض مثلاً)
            
            $table->timestamps();
            
            // Indexes
            $table->index(['task_id', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_notes');
    }
};
