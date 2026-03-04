<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectWorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_workflow_id',
        'workflow_step_id',
        'name',
        'description',
        'order',
        'department',
        'duration_days',
        'status',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'delay_days',
        'expected_outputs',
        'actual_outputs',
        'assigned_to',
        'notes',
        'is_custom',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_days' => 'integer',
        'delay_days' => 'integer',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'expected_outputs' => 'array',
        'actual_outputs' => 'array',
        'is_custom' => 'boolean',
    ];

    /**
     * مسار المشروع المرتبط
     */
    public function projectWorkflow()
    {
        return $this->belongsTo(ProjectWorkflow::class);
    }

    /**
     * الخطوة من القالب (إن وجدت)
     */
    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /**
     * المسؤول عن الخطوة
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * تحديث حالة الخطوة
     */
    public function updateStatus(string $status): void
    {
        $this->status = $status;
        
        if ($status === 'in_progress' && !$this->start_date) {
            $this->start_date = Carbon::now();
        }
        
        if ($status === 'completed' && !$this->actual_end_date) {
            $this->actual_end_date = Carbon::now();
            
            // حساب التأخير
            if ($this->expected_end_date) {
                $delay = Carbon::now()->diffInDays($this->expected_end_date, false);
                $this->delay_days = $delay > 0 ? $delay : 0;
            }
        }
        
        $this->save();
        
        // تحديث تقدم المسار
        if ($this->projectWorkflow) {
            $this->projectWorkflow->updateProgress();
        }
    }

    /**
     * حساب تاريخ الانتهاء المتوقع
     */
    public function calculateExpectedEndDate(): ?Carbon
    {
        if (!$this->start_date) {
            return null;
        }
        
        return Carbon::parse($this->start_date)->addDays($this->duration_days);
    }

    /**
     * الخطوات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * الخطوات قيد التنفيذ
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * الخطوات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
