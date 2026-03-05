<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add name, name_en, code, order, deleted_at to cities if missing.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('cities', 'name_en')) {
                $table->string('name_en')->nullable();
            }
            if (!Schema::hasColumn('cities', 'code')) {
                $table->string('code')->nullable();
            }
            if (!Schema::hasColumn('cities', 'order')) {
                $table->integer('order')->default(0);
            }
            if (!Schema::hasColumn('cities', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $cols = ['name', 'name_en', 'code', 'order'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('cities', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('cities', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
