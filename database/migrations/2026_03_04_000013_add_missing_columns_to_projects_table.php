<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add missing columns to projects table for CRM compatibility.
     */
    public function up(): void
    {
        $columns = [
            'name' => fn($t) => $t->string('name')->nullable(),
            'project_number' => fn($t) => $t->string('project_number')->nullable(),
            'type' => fn($t) => $t->string('type')->nullable(),
            'city' => fn($t) => $t->string('city')->nullable(),
            'district' => fn($t) => $t->string('district')->nullable(),
            'contract_number' => fn($t) => $t->string('contract_number')->nullable(),
            'contract_file' => fn($t) => $t->string('contract_file')->nullable(),
            'land_number' => fn($t) => $t->string('land_number')->nullable(),
            'plan_file' => fn($t) => $t->string('plan_file')->nullable(),
            'baladi_request_number' => fn($t) => $t->string('baladi_request_number')->nullable(),
            'stages' => fn($t) => $t->json('stages')->nullable(),
            'current_stage' => fn($t) => $t->string('current_stage')->nullable(),
            'project_manager_id' => fn($t) => $t->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete(),
            'team_members' => fn($t) => $t->json('team_members')->nullable(),
            'internal_notes' => fn($t) => $t->text('internal_notes')->nullable(),
            'start_date' => fn($t) => $t->date('start_date')->nullable(),
            'end_date' => fn($t) => $t->date('end_date')->nullable(),
        ];

        foreach ($columns as $col => $callback) {
            if (!Schema::hasColumn('projects', $col)) {
                Schema::table('projects', function (Blueprint $table) use ($callback) {
                    $callback($table);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing - would require careful handling of foreign keys
    }
};
