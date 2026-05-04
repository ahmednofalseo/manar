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
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (! Schema::hasColumn('projects', 'owner_en')) {
                $table->string('owner_en')->nullable()->after('owner');
            }
            if (! Schema::hasColumn('projects', 'district_en')) {
                $table->string('district_en')->nullable()->after('district');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $columns = collect(['name_en', 'owner_en', 'district_en'])
            ->filter(fn (string $c) => Schema::hasColumn('projects', $c))
            ->values()
            ->all();

        if ($columns !== []) {
            Schema::table('projects', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
