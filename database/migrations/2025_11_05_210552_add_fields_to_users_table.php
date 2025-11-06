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
            $table->string('phone')->nullable()->after('email');
            $table->string('national_id')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('national_id');
            $table->string('practice_license_no')->nullable()->after('job_title');
            $table->date('engineer_rank_expiry')->nullable()->after('practice_license_no');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('engineer_rank_expiry');
            $table->string('avatar')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
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
