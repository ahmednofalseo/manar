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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم المستهدف
            $table->string('type'); // task_assigned, task_comment
            $table->morphs('notifiable'); // task_id
            $table->text('title'); // عنوان الإشعار
            $table->text('message'); // رسالة الإشعار
            $table->text('data')->nullable(); // بيانات إضافية (JSON)
            $table->boolean('read')->default(false); // تم القراءة
            $table->timestamp('read_at')->nullable(); // تاريخ القراءة
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
