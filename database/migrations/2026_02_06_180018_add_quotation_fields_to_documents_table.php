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
            if (!Schema::hasColumn('documents', 'issue_date')) {
                $column = $table->date('issue_date')->nullable();
                if (Schema::hasColumn('documents', 'expires_at')) {
                    $column->after('expires_at');
                }
            }
            if (!Schema::hasColumn('documents', 'valid_until')) {
                $column = $table->date('valid_until')->nullable();
                if (Schema::hasColumn('documents', 'issue_date')) {
                    $column->after('issue_date');
                }
            }

            // الحقول المالية (إذا لم تكن موجودة)
            if (!Schema::hasColumn('documents', 'subtotal')) {
                $column = $table->decimal('subtotal', 15, 2)->nullable();
                if (Schema::hasColumn('documents', 'total_price')) {
                    $column->after('total_price');
                }
            }
            if (!Schema::hasColumn('documents', 'discount_type')) {
                $column = $table->enum('discount_type', ['amount', 'percent'])->nullable();
                if (Schema::hasColumn('documents', 'subtotal')) {
                    $column->after('subtotal');
                }
            }
            if (!Schema::hasColumn('documents', 'discount_value')) {
                $column = $table->decimal('discount_value', 15, 2)->nullable();
                if (Schema::hasColumn('documents', 'discount_type')) {
                    $column->after('discount_type');
                }
            }
            if (!Schema::hasColumn('documents', 'vat_percent')) {
                $column = $table->decimal('vat_percent', 5, 2)->nullable();
                if (Schema::hasColumn('documents', 'discount_value')) {
                    $column->after('discount_value');
                }
            }
            if (!Schema::hasColumn('documents', 'vat_amount')) {
                $column = $table->decimal('vat_amount', 15, 2)->nullable();
                if (Schema::hasColumn('documents', 'vat_percent')) {
                    $column->after('vat_percent');
                }
            }
            if (!Schema::hasColumn('documents', 'total_in_words')) {
                $column = $table->string('total_in_words')->nullable();
                if (Schema::hasColumn('documents', 'total_price')) {
                    $column->after('total_price');
                }
            }
            if (!Schema::hasColumn('documents', 'terms_html')) {
                $column = $table->longText('terms_html')->nullable();
                if (Schema::hasColumn('documents', 'content')) {
                    $column->after('content');
                }
            }
            if (!Schema::hasColumn('documents', 'notes_internal')) {
                $column = $table->text('notes_internal')->nullable();
                if (Schema::hasColumn('documents', 'terms_html')) {
                    $column->after('terms_html');
                }
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
