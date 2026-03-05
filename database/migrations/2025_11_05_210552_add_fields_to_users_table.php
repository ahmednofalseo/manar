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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $column = $table->string('phone')->nullable();
                if (Schema::hasColumn('users', 'email')) {
                    $column->after('email');
                }
            }
            if (!Schema::hasColumn('users', 'national_id')) {
                $column = $table->string('national_id')->nullable();
                if (Schema::hasColumn('users', 'phone')) {
                    $column->after('phone');
                }
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $column = $table->string('job_title')->nullable();
                if (Schema::hasColumn('users', 'national_id')) {
                    $column->after('national_id');
                }
            }
            if (!Schema::hasColumn('users', 'practice_license_no')) {
                $column = $table->string('practice_license_no')->nullable();
                if (Schema::hasColumn('users', 'job_title')) {
                    $column->after('job_title');
                }
            }
            if (!Schema::hasColumn('users', 'engineer_rank_expiry')) {
                $column = $table->date('engineer_rank_expiry')->nullable();
                if (Schema::hasColumn('users', 'practice_license_no')) {
                    $column->after('practice_license_no');
                }
            }
            if (!Schema::hasColumn('users', 'status')) {
                $column = $table->enum('status', ['active', 'suspended'])->default('active');
                if (Schema::hasColumn('users', 'engineer_rank_expiry')) {
                    $column->after('engineer_rank_expiry');
                }
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $column = $table->string('avatar')->nullable();
                if (Schema::hasColumn('users', 'status')) {
                    $column->after('status');
                }
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $column = $table->timestamp('last_login_at')->nullable();
                if (Schema::hasColumn('users', 'avatar')) {
                    $column->after('avatar');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'national_id',
                'job_title',
                'practice_license_no',
                'engineer_rank_expiry',
                'status',
                'avatar',
                'last_login_at'
            ]);
        });
    }
};
