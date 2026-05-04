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
            'status_change' => __('Task note action status change'),
            'rejection' => __('Task note action rejection'),
            'comment' => __('Task note action comment'),
            'attachment' => __('Task note action attachment'),
            'reopen' => __('Task note action reopen'),
            'assignment' => __('Task note action assignment'),
        ];

        $actionName = $actions[$this->action_type] ?? $this->action_type;

        if ($this->action_type === 'status_change' && $this->old_value && $this->new_value) {
            $statusMap = [
                'new' => __('Task status new'),
                'in_progress' => __('Task status in progress'),
                'done' => __('Task status done'),
                'rejected' => __('Task status rejected'),
            ];
            $old = $statusMap[$this->old_value] ?? $this->old_value;
            $new = $statusMap[$this->new_value] ?? $this->new_value;

            return __('Task note status change line', [
                'action' => $actionName,
                'from' => $old,
                'to' => $new,
            ]);
        }

        return $actionName;
    }
}
