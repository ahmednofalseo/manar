<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\ProjectStageStatus;
use App\Models\Project;
use App\Models\ProjectStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectStagePlanner
{
    /**
     * @param  list<array{id?: int|string|null, name: string, amount?: numeric-string|float|int|null}>  $rows
     */
    public function sync(Project $project, array $rows): void
    {
        DB::transaction(function () use ($project, $rows) {
            $keptIds = [];
            $hasOpenStage = $project->projectStages()
                ->whereIn('status', [ProjectStageStatus::Active, ProjectStageStatus::AwaitingPayment])
                ->exists();

            foreach (array_values($rows) as $index => $row) {
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $stage = null;
                if (! empty($row['id'])) {
                    $stage = $project->projectStages()->whereKey($row['id'])->first();
                }

                $amount = round((float) ($row['amount'] ?? 0), 2);

                if (! $stage) {
                    $status = $hasOpenStage ? ProjectStageStatus::Locked : ProjectStageStatus::Active;

                    $stage = $project->projectStages()->create([
                        'stage_name' => $name,
                        'sort_order' => $index,
                        'invoice_amount' => $amount,
                        'status' => $status,
                        'progress' => 0,
                        'start_date' => $status === ProjectStageStatus::Active ? now()->toDateString() : null,
                    ]);
                    $hasOpenStage = true;
                } else {
                    $payload = [
                        'stage_name' => $name,
                        'sort_order' => $index,
                    ];
                    if ($stage->status !== ProjectStageStatus::Completed) {
                        $payload['invoice_amount'] = $amount;
                    }
                    $stage->update($payload);
                    $this->syncUnpaidInvoiceAmount($stage, $amount);
                }

                $keptIds[] = $stage->id;
            }

            $removable = $project->projectStages()->whereNotIn('id', $keptIds ?: [0])->get();
            foreach ($removable as $stage) {
                if ($stage->tasks()->exists() || $stage->invoice()->exists()) {
                    throw ValidationException::withMessages([
                        'stage_plan' => __('Cannot remove a stage that already has tasks or an invoice.'),
                    ]);
                }
                $stage->delete();
            }

            $orderedNames = $project->projectStages()->orderBy('sort_order')->pluck('stage_name')->all();
            $current = $project->projectStages()
                ->whereIn('status', [ProjectStageStatus::Active, ProjectStageStatus::AwaitingPayment])
                ->orderBy('sort_order')
                ->value('stage_name');

            $project->update([
                'stages' => $orderedNames,
                'current_stage' => $current,
            ]);
        });
    }

    protected function syncUnpaidInvoiceAmount(ProjectStage $stage, float $amount): void
    {
        $invoice = $stage->invoice;
        if (! $invoice || $invoice->status === InvoiceStatus::PAID) {
            return;
        }

        if ((float) $invoice->paid_amount > $amount) {
            return;
        }

        $invoice->update(['total_amount' => $amount]);
        $invoice->updatePaidAmountAndStatus();
    }
}
