<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin/Finance/PM: يمكنهم رؤية كل شيء
        return $user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        // Admin/Finance/PM: يمكنهم رؤية كل شيء
        if ($user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager'])) {
            return true;
        }

        // Engineer: يمكنه رؤية مصروفاته فقط
        if ($user->hasRole('engineer')) {
            return $expense->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin/Finance/PM/Engineer: يمكنهم إضافة مصروفات
        return $user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager', 'engineer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Admin/Finance: يمكنهم تعديل أي مصروف
        if ($user->hasAnyRole(['super_admin', 'admin_staff'])) {
            return true;
        }

        // PM: يمكنه تعديل مصروفات مشاريعه
        if ($user->hasRole('project_manager')) {
            // يمكن إضافة منطق إضافي هنا
            return true;
        }

        // Engineer: يمكنه تعديل مصروفاته فقط إذا كانت pending
        if ($user->hasRole('engineer')) {
            return $expense->created_by === $user->id 
                && $expense->status === \App\Enums\ExpenseStatus::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Admin: يمكنه حذف أي مصروف
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Finance: يمكنه حذف المصروفات المعتمدة فقط
        if ($user->hasRole('admin_staff')) {
            return $expense->status === \App\Enums\ExpenseStatus::APPROVED;
        }

        // Engineer: يمكنه حذف مصروفاته فقط إذا كانت pending
        if ($user->hasRole('engineer')) {
            return $expense->created_by === $user->id 
                && $expense->status === \App\Enums\ExpenseStatus::PENDING;
        }

        return false;
    }

    /**
     * Determine whether the user can approve expenses.
     */
    public function approve(User $user, Expense $expense): bool
    {
        // Admin/Finance/PM: يمكنهم اعتماد المصروفات
        return $user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager'])
            && $expense->status === \App\Enums\ExpenseStatus::PENDING;
    }

    /**
     * Determine whether the user can reject expenses.
     */
    public function reject(User $user, Expense $expense): bool
    {
        // Admin/Finance/PM: يمكنهم رفض المصروفات
        return $user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager'])
            && $expense->status === \App\Enums\ExpenseStatus::PENDING;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->hasRole('super_admin');
    }
}
