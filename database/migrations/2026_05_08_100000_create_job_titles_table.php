<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_titles')) {
            return;
        }

        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['name' => 'مهندس معماري', 'name_en' => 'Architectural Engineer', 'order' => 1],
            ['name' => 'مهندس إنشائي', 'name_en' => 'Structural Engineer', 'order' => 2],
            ['name' => 'مهندس كهرباء', 'name_en' => 'Electrical Engineer', 'order' => 3],
            ['name' => 'مهندس ميكانيكي', 'name_en' => 'Mechanical Engineer', 'order' => 4],
            ['name' => 'مدير مشروع', 'name_en' => 'Project Manager', 'order' => 5],
            ['name' => 'إداري', 'name_en' => 'Administrative', 'order' => 6],
        ];

        foreach ($defaults as $row) {
            DB::table('job_titles')->insert([
                'name' => $row['name'],
                'name_en' => $row['name_en'],
                'is_active' => true,
                'order' => $row['order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_titles');
    }
};
