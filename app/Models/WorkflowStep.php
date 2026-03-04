<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'name',
        'description',
        'order',
        'department',
        'default_duration_days',
        'expected_outputs',
        'dependencies',
        'is_parallel',
        'is_required',
    ];

    protected $casts = [
        'order' => 'integer',
        'default_duration_days' => 'integer',
        'expected_outputs' => 'array',
        'dependencies' => 'array',
        'is_parallel' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * القالب المرتبط
     */
    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    /**
     * خطوات مسارات المشاريع المرتبطة
     */
    public function projectWorkflowSteps()
    {
        return $this->hasMany(ProjectWorkflowStep::class);
    }
}
