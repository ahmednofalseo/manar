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
        if (Schema::hasTable('workflow_templates')) {
            return;
        }
        
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // الخدمة المرتبطة
            $table->string('name'); // اسم القالب
            $table->text('description')->nullable(); // وصف القالب
            $table->boolean('is_default')->default(true); // قالب افتراضي
            $table->boolean('is_active')->default(true); // نشط/غير نشط
            $table->timestamps();
            
            // Indexes
            $table->index('service_id');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_templates');
    }
};
