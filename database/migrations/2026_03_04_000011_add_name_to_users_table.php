<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add name column to users (for tables that have fullname instead).
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // Populate from fullname if exists
        if (Schema::hasColumn('users', 'fullname')) {
            DB::table('users')->whereNull('name')->update(['name' => DB::raw('fullname')]);
        }
        // Or from first_name + last_name
        elseif (Schema::hasColumn('users', 'first_name') && Schema::hasColumn('users', 'last_name')) {
            DB::statement("UPDATE users SET name = CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) WHERE name IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
