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
        Schema::table('documents', function (Blueprint $table) {
            // حقول إضافية لعروض الأسعار المنظمة
            $table->date('issue_date')->nullable()->after('expires_at'); // تاريخ الإصدار
            $table->date('valid_until')->nullable()->after('issue_date'); // صالح حتى
            
            // الحقول المالية (إذا لم تكن موجودة)
            if (!Schema::hasColumn('documents', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->nullable()->after('total_price'); // الإجمالي الفرعي
            }
            if (!Schema::hasColumn('documents', 'discount_type')) {
                $table->enum('discount_type', ['amount', 'percent'])->nullable()->after('subtotal'); // نوع الخصم
            }
            if (!Schema::hasColumn('documents', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type'); // قيمة الخصم
            }
            if (!Schema::hasColumn('documents', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->nullable()->after('discount_value'); // نسبة الضريبة
            }
            if (!Schema::hasColumn('documents', 'vat_amount')) {
                $table->decimal('vat_amount', 15, 2)->nullable()->after('vat_percent'); // مبلغ الضريبة
            }
            if (!Schema::hasColumn('documents', 'total_in_words')) {
                $table->string('total_in_words')->nullable()->after('total_price'); // الإجمالي بالحروف
            }
            if (!Schema::hasColumn('documents', 'terms_html')) {
                $table->longText('terms_html')->nullable()->after('content'); // الشروط والأحكام (HTML)
            }
            if (!Schema::hasColumn('documents', 'notes_internal')) {
                $table->text('notes_internal')->nullable()->after('terms_html'); // ملاحظات داخلية
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'issue_date',
                'valid_until',
                'subtotal',
                'discount_type',
                'discount_value',
                'vat_percent',
                'vat_amount',
                'total_in_words',
                'terms_html',
                'notes_internal',
            ]);
        });
    }
};
