<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'service_id',
        'workflow_template_id',
        'name',
        'status',
        'is_parallel',
        'parent_workflow_id',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'progress',
        'notes',
    ];

    protected $casts = [
        'is_parallel' => 'boolean',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'progress' => 'integer',
    ];

    /**
     * المشروع المرتبط
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * الخدمة المرتبطة
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * القالب المستخدم
     */
    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    /**
     * المسار الرئيسي (للمسارات المتوازية)
     */
    public function parentWorkflow()
    {
        return $this->belongsTo(ProjectWorkflow::class, 'parent_workflow_id');
    }

    /**
     * المسارات الفرعية المتوازية
     */
    public function parallelWorkflows()
    {
        return $this->hasMany(ProjectWorkflow::class, 'parent_workflow_id');
    }

    /**
     * خطوات المسار
     */
    public function steps()
    {
        return $this->hasMany(ProjectWorkflowStep::class)->orderBy('order');
    }

    /**
     * حساب نسبة التقدم
     */
    public function calculateProgress(): int
    {
        $totalSteps = $this->steps()->count();
        if ($totalSteps === 0) {
            return 0;
        }

        $completedSteps = $this->steps()->where('status', 'completed')->count();
        return round(($completedSteps / $totalSteps) * 100);
    }

    /**
     * تحديث نسبة التقدم تلقائياً
     */
    public function updateProgress(): void
    {
        $this->progress = $this->calculateProgress();
        $this->save();
    }

    /**
     * التحقق من التأخير
     */
    public function checkDelays(): void
    {
        $now = Carbon::now();
        foreach ($this->steps()->where('status', '!=', 'completed')->get() as $step) {
            if ($step->expected_end_date && $now->greaterThan($step->expected_end_date)) {
                $delayDays = $now->diffInDays($step->expected_end_date);
                $step->delay_days = $delayDays;
                $step->save();
            }
        }
    }

    /**
     * المسارات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * المسارات المتوازية
     */
    public function scopeParallel($query)
    {
        return $query->where('is_parallel', true);
    }
}
