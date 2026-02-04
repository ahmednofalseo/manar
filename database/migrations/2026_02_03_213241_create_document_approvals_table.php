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
        if (Schema::hasTable('document_approvals')) {
            return;
        }
        
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('approved_by')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['approved', 'rejected']);
            $table->text('reason')->nullable(); // سبب الرفض أو ملاحظات
            $table->timestamps();
            
            $table->index('document_id');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};
