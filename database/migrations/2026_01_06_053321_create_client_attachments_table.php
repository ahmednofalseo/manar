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
        if (!Schema::hasTable('client_attachments')) {
            Schema::create('client_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->string('name'); // اسم الملف
                $table->string('file_path'); // مسار الملف
                $table->string('file_type')->nullable(); // نوع الملف
                $table->unsignedBigInteger('file_size')->nullable(); // حجم الملف
                $table->string('category')->nullable(); // نوع المرفق (هوية، سجل تجاري، عقد، إلخ)
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_attachments');
    }
};
