<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // جميع المستخدمين يمكنهم رؤية المهام (مع فلاتر حسب الصلاحيات)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Super Admin: يرى كل شيء
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // تحميل العلاقات إذا لم تكن محملة
        if (!$task->relationLoaded('project')) {
            $task->load('project');
        }

        // Project Manager: يرى مهام مشاريعه
        if ($user->hasRole('project_manager')) {
            if ($task->project->project_manager_id === $user->id) {
                return true;
            }
            
            // التحقق من عضوية الفريق
            if (!$task->project->relationLoaded('teamUsers')) {
                $task->project->load('teamUsers');
            }
            
            return $task->project->teamUsers->pluck('id')->contains($user->id);
        }

        // Employee: يرى مهامه فقط
        if ($user->hasRole('engineer') || $user->hasRole('admin_staff')) {
            return $task->assignee_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // فقط Managers و Admins
        return $user->hasRole('super_admin') || $user->hasRole('project_manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Super Admin: يمكنه التعديل الكامل
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Project Manager: يمكنه تعديل مهام مشاريعه
        if ($user->hasRole('project_manager')) {
            // تحميل المشروع إذا لم يكن محملاً
            if (!$task->relationLoaded('project')) {
                $task->load('project');
            }
            return $task->project->project_manager_id === $user->id;
        }

        // Employee: يمكنه تحديث مهامه فقط (حقول محدودة)
        if (($user->hasRole('engineer') || $user->hasRole('admin_staff')) && $task->assignee_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // فقط Super Admin و Project Manager
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('project_manager')) {
            return $task->project->project_manager_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can change task status.
     */
    public function changeStatus(User $user, Task $task): bool
    {
        // Super Admin و Project Manager: يمكنهم تغيير أي حالة
        if ($user->hasRole('super_admin') || $user->hasRole('project_manager')) {
            return true;
        }

        // Employee: يمكنه تغيير حالة مهامه فقط (من new إلى in_progress إلى done)
        if ($user->hasRole('engineer') && $task->assignee_id === $user->id) {
            return in_array($task->status, ['new', 'in_progress']);
        }

        return false;
    }

    /**
     * Determine whether the user can reject the task.
     */
    public function reject(User $user, Task $task): bool
    {
        // فقط Super Admin و Project Manager يمكنهم الرفض
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('project_manager')) {
            return $task->project->project_manager_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can add notes/comments.
     */
    public function addNote(User $user, Task $task): bool
    {
        // أي شخص له صلاحية عرض المهمة يمكنه إضافة ملاحظة
        return $this->view($user, $task);
    }
}
