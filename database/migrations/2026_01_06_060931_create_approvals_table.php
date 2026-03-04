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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            
            // Polymorphic relationship للعنصر المطلوب الموافقة عليه
            $table->morphs('approvable'); // approvable_type, approvable_id
            
            // المرحلة
            $table->string('stage_key'); // architectural, structural, electrical, mechanical, municipality, health_environmental, civil_defense
            
            // الحالة
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // الملاحظات
            $table->text('client_note')->nullable(); // ملاحظة العميل عند الموافقة/الرفض
            $table->text('manager_note')->nullable(); // ملاحظة المدير عند الرفض أو إعادة المراجعة
            
            // المستخدمون
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade'); // من طلب الموافقة
            $table->foreignId('decided_by')->nullable()->constrained('users')->onDelete('set null'); // من قرر الموافقة/الرفض
            
            // التواريخ
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('decided_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index('stage_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
