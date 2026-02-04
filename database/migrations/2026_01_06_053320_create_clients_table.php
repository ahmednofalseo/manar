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
        if (Schema::hasTable('clients')) {
            return;
        }
        
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم العميل
            $table->enum('type', ['individual', 'company', 'government']); // نوع العميل
            $table->string('national_id_or_cr')->nullable(); // رقم الهوية / السجل التجاري
            $table->string('phone'); // رقم الجوال
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('city'); // المدينة
            $table->string('district')->nullable(); // الحي
            $table->text('address')->nullable(); // العنوان
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->text('notes_internal')->nullable(); // ملاحظات داخلية
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
