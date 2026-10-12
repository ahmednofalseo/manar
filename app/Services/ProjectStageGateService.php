<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\ProjectStageStatus;
use App\Models\Invoice;
use App\Models\ProjectStage;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class ProjectStageGateService
{
    public function handleTaskCompleted(Task $task): void
    {
        $task->loadMissing('projectStage.tasks', 'projectStage.project');
        $stage = $task->projectStage;

        if (! $stage || $stage->status !== ProjectStageStatus::Active) {
            return;
        }

        $this->refreshProgress($stage);

        if ($this->hasOpenTasks($stage)) {
            return;
        }

        $this->issueInvoice($stage);
    }

    public function closeActiveStage(ProjectStage $stage): void
    {
        if ($stage->status !== ProjectStageStatus::Active) {
            throw new \InvalidArgumentException(__('Only the active stage can be closed.'));
        }

        if ($this->hasOpenTasks($stage)) {
            throw new \InvalidArgumentException(__('Finish the stage tasks before issuing its invoice.'));
        }

        $this->issueInvoice($stage);
    }

    public function handleInvoicePaid(Invoice $invoice): void
    {
        if ($invoice->status !== InvoiceStatus::PAID || ! $invoice->project_stage_id) {
            return;
        }

        $invoice->loadMissing('projectStage.project');
        $stage = $invoice->projectStage;

        if (! $stage || $stage->status === ProjectStageStatus::Completed) {
            return;
        }

        DB::transaction(function () use ($stage) {
            $stage->update([
                'status' => ProjectStageStatus::Completed,
                'progress' => 100,
                'end_date' => now()->toDateString(),
            ]);

            $next = $stage->project->projectStages()
                ->where('sort_order', '>', $stage->sort_order)
                ->where('status', ProjectStageStatus::Locked)
                ->orderBy('sort_order')
                ->first();

            if ($next) {
                $next->update([
                    'status' => ProjectStageStatus::Active,
                    'start_date' => now()->toDateString(),
                ]);
                $stage->project->update(['current_stage' => $next->stage_name]);
            } else {
                $stage->project->update(['current_stage' => null]);
            }

            $stage->project->progress = $stage->project->calculateProgress();
            $stage->project->save();
        });
    }

    public function hasOpenTasks(ProjectStage $stage): bool
    {
        return $stage->tasks()->whereNotIn('status', ['done', 'rejected'])->exists();
    }

    protected function issueInvoice(ProjectStage $stage): void
    {
        if ($stage->invoice()->exists()) {
            $invoice = $stage->invoice;
            if ($invoice->status === InvoiceStatus::PAID) {
                $this->handleInvoicePaid($invoice);

                return;
            }

            $stage->update(['status' => ProjectStageStatus::AwaitingPayment]);

            return;
        }

        $stage->loadMissing('project');

        DB::transaction(function () use ($stage) {
            $invoice = Invoice::create([
                'project_id' => $stage->project_id,
                'project_stage_id' => $stage->id,
                'client_id' => $stage->project->client_id,
                'number' => Invoice::generateInvoiceNumber(),
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'total_amount' => $stage->invoice_amount,
                'paid_amount' => 0,
                'status' => (float) $stage->invoice_amount <= 0 ? InvoiceStatus::PAID : InvoiceStatus::UNPAID,
                'notes' => __('Stage invoice note', ['stage' => $stage->stage_name]),
            ]);

            $stage->update([
                'status' => ProjectStageStatus::AwaitingPayment,
                'progress' => 100,
            ]);

            if ((float) $stage->invoice_amount <= 0) {
                $this->handleInvoicePaid($invoice);
            }
        });
    }

    protected function refreshProgress(ProjectStage $stage): void
    {
        $total = $stage->tasks()->count();
        $done = $stage->tasks()->where('status', 'done')->count();
        $stage->update([
            'progress' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
        ]);
    }
}
