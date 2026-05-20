<?php

namespace App\Policies;

use App\Enums\ExpenseStatus;
use App\Models\Expense;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ExpensePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'expenses');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $this->canViewModule($user, 'expenses');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'expenses');
    }

    public function update(User $user, Expense $expense): bool
    {
        return $this->canUpdateModule($user, 'expenses');
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $this->canDeleteModule($user, 'expenses');
    }

    public function approve(User $user, Expense $expense): bool
    {
        return ($user->hasPermission('expenses.approve') || $this->canManageModule($user, 'expenses'))
            && $expense->status === ExpenseStatus::PENDING;
    }

    public function reject(User $user, Expense $expense): bool
    {
        return $this->approve($user, $expense);
    }

    public function restore(User $user, Expense $expense): bool
    {
        return $this->canManageModule($user, 'expenses');
    }

    public function forceDelete(User $user, Expense $expense): bool
    {
        return $this->canManageModule($user, 'expenses');
    }
}
