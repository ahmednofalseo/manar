<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'project_stage_id',
        'assignee_id',
        'created_by',
        'title',
        'description',
        'manager_notes',
        'status',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'priority',
        'start_date',
        'due_date',
        'completed_at',
        'progress',
        'completion_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'progress' => 'integer',
    ];

    /**
     * المشروع
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * مرحلة المشروع
     */
    public function projectStage()
    {
        return $this->belongsTo(ProjectStage::class);
    }

    /**
     * الموظف المسند إليه
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * من أنشأ المهمة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من رفض المهمة
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * ملاحظات/سجل المهمة (audit trail)
     */
    public function notes()
    {
        return $this->hasMany(TaskNote::class)->latest();
    }

    /**
     * المرفقات
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    /**
     * التحقق من إمكانية تغيير الحالة
     */
    public function canChangeStatus($newStatus): bool
    {
        // إذا كانت الحالة الجديدة نفس الحالة الحالية، لا حاجة للتغيير
        if ($this->status === $newStatus) {
            return false;
        }

        // السماح بجميع الانتقالات (لأن الإدارة/المدير يمكنهم تغيير الحالة لأي حالة)
        // يمكن تقييد الانتقالات هنا إذا لزم الأمر
        
        $validStatuses = ['new', 'in_progress', 'done', 'rejected'];
        return in_array($newStatus, $validStatuses);
    }

    /**
     * التحقق من أن المرحلة مرتبطة بالمشروع
     */
    public function validateStage(): bool
    {
        if (!$this->project_stage_id) {
            return true; // المرحلة اختيارية
        }

        return $this->project->projectStages()
            ->where('id', $this->project_stage_id)
            ->exists();
    }

    /**
     * تسجيل تغيير في سجل المهمة
     */
    public function logActivity($actionType, $userId, $oldValue = null, $newValue = null, $notes = null, $reason = null)
    {
        return TaskNote::create([
            'task_id' => $this->id,
            'user_id' => $userId,
            'action_type' => $actionType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes' => $notes,
            'reason' => $reason,
        ]);
    }

    /**
     * Scope للمهام المسندة لمستخدم
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assignee_id', $userId);
    }

    /**
     * Scope لمهام مشروع
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope لحالة محددة
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope للمهام المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['done', 'rejected']);
    }
}
