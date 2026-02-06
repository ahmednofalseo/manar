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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('item_name'); // اسم البند
            $table->text('description')->nullable(); // وصف البند
            $table->decimal('qty', 10, 2)->default(1); // الكمية
            $table->string('unit')->default('قطعة'); // الوحدة (قطعة، متر، ساعة، إلخ)
            $table->decimal('unit_price', 15, 2); // سعر الوحدة
            $table->decimal('line_total', 15, 2); // الإجمالي (qty * unit_price)
            $table->integer('position')->default(0); // ترتيب البند
            $table->timestamps();
            
            $table->index('document_id');
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
