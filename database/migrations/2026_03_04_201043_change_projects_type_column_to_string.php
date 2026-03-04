<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * تغيير عمود type من ENUM إلى string ليقبل أي قيمة من جدول project_types
     * يحل مشكلة: Data truncated for column 'type' عند اختلاف التهجئة (مثل: تصميم و اشراف vs تصميم وإشراف)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE projects MODIFY COLUMN type VARCHAR(100) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE projects ALTER COLUMN type TYPE VARCHAR(100) USING type::text');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN type ENUM(
                'تصميم',
                'تصميم وإشراف',
                'إشراف',
                'تقرير فني',
                'تقرير دفاع مدني',
                'تصميم دفاع مدني',
                'تعديلات',
                'استشارات'
            ) NOT NULL");
        }
        // pgsql: لا يمكن استعادة ENUM بسهولة - يمكن إضافة check constraint
    }
};
