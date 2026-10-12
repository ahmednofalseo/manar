<?php

use App\Enums\ProjectStageStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_stages', function (Blueprint $table) {
            if (! Schema::hasColumn('project_stages', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('stage_name');
            }
            if (! Schema::hasColumn('project_stages', 'invoice_amount')) {
                $table->decimal('invoice_amount', 15, 2)->default(0)->after('sort_order');
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            $type = Schema::getColumnType('project_stages', 'status');
            if ($type !== 'string') {
                DB::statement("ALTER TABLE project_stages MODIFY status VARCHAR(32) NOT NULL DEFAULT 'locked'");
            }
        }

        DB::table('project_stages')->where('status', 'مكتمل')->update(['status' => ProjectStageStatus::Completed->value]);
        DB::table('project_stages')->where('status', 'جارٍ')->update(['status' => ProjectStageStatus::Active->value]);
        DB::table('project_stages')->where('status', 'جديد')->update(['status' => ProjectStageStatus::Locked->value]);

        $projects = DB::table('project_stages')->select('project_id')->distinct()->pluck('project_id');
        foreach ($projects as $projectId) {
            $stages = DB::table('project_stages')->where('project_id', $projectId)->orderBy('id')->get();
            foreach ($stages as $index => $stage) {
                DB::table('project_stages')->where('id', $stage->id)->update(['sort_order' => $index]);
            }

            $hasOpen = $stages->contains(fn ($stage) => in_array($stage->status, [
                ProjectStageStatus::Active->value,
                ProjectStageStatus::AwaitingPayment->value,
                ProjectStageStatus::Completed->value,
            ], true));

            if (! $hasOpen && $stages->isNotEmpty()) {
                DB::table('project_stages')->where('id', $stages->first()->id)->update([
                    'status' => ProjectStageStatus::Active->value,
                    'start_date' => now()->toDateString(),
                ]);
            }
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'project_stage_id')) {
                $table->foreignId('project_stage_id')->nullable()->after('project_id')->constrained('project_stages')->nullOnDelete();
                $table->unique('project_stage_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'project_stage_id')) {
                $table->dropUnique(['project_stage_id']);
                $table->dropConstrainedForeignId('project_stage_id');
            }
        });

        Schema::table('project_stages', function (Blueprint $table) {
            if (Schema::hasColumn('project_stages', 'invoice_amount')) {
                $table->dropColumn('invoice_amount');
            }
            if (Schema::hasColumn('project_stages', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
