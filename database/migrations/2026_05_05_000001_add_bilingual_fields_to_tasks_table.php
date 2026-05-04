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
        if (! Schema::hasTable('tasks')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }
            if (! Schema::hasColumn('tasks', 'description_en')) {
                $table->text('description_en')->nullable()->after('description');
            }
            if (! Schema::hasColumn('tasks', 'manager_notes_en')) {
                $table->text('manager_notes_en')->nullable()->after('manager_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $columns = collect(['title_en', 'description_en', 'manager_notes_en'])
            ->filter(fn (string $c) => Schema::hasColumn('tasks', $c))
            ->values()
            ->all();

        if ($columns !== []) {
            Schema::table('tasks', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
