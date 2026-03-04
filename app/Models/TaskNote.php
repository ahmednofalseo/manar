<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'action_type',
        'old_value',
        'new_value',
        'notes',
        'reason',
    ];

    /**
     * المهمة
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * المستخدم الذي قام بالإجراء
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على نص وصف للإجراء
     */
    public function getActionDescriptionAttribute(): string
    {
        $actions = [
            'status_change' => 'تغيير الحالة',
            'rejection' => 'رفض المهمة',
            'comment' => 'تعليق',
            'attachment' => 'رفع مرفق',
            'reopen' => 'إعادة فتح',
            'assignment' => 'إسناد',
        ];

        $actionName = $actions[$this->action_type] ?? $this->action_type;

        if ($this->action_type === 'status_change' && $this->old_value && $this->new_value) {
            $statusMap = [
                'new' => 'جديد',
                'in_progress' => 'قيد التنفيذ',
                'done' => 'منجز',
                'rejected' => 'مرفوض',
            ];
            $old = $statusMap[$this->old_value] ?? $this->old_value;
            $new = $statusMap[$this->new_value] ?? $this->new_value;
            return "{$actionName}: من {$old} إلى {$new}";
        }

        return $actionName;
    }
}
