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
        if (Schema::hasTable('document_templates')) {
            return;
        }
        
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القالب
            $table->enum('type', ['technical_report', 'quotation']); // نوع المستند
            $table->text('content')->nullable(); // محتوى القالب (HTML)
            $table->json('variables')->nullable(); // المتغيرات المتاحة
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
