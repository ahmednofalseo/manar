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
        if (Schema::hasTable('documents')) {
            return;
        }
        
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique(); // رقم المستند
            $table->enum('type', ['technical_report', 'quotation']); // نوع المستند
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('title'); // عنوان المستند
            $table->longText('content'); // محتوى المستند (HTML)
            $table->enum('status', [
                // التقارير الفنية
                'draft', 'submitted', 'approved', 'rejected',
                // عروض الأسعار
                'sent', 'accepted', 'expired'
            ])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable(); // سبب الرفض
            $table->decimal('total_price', 15, 2)->nullable(); // للسعر (لعروض الأسعار)
            $table->date('expires_at')->nullable(); // تاريخ انتهاء العرض
            $table->string('pdf_path')->nullable(); // مسار ملف PDF
            $table->timestamps();
            
            $table->index('type');
            $table->index('status');
            $table->index('document_number');
            $table->index('created_by');
            $table->index('project_id');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
