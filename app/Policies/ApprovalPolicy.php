<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ApprovalPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'approvals');
    }

    public function view(User $user, Approval $approval): bool
    {
        return $this->canViewModule($user, 'approvals');
    }

    public function request(User $user, ?Approval $approval = null): bool
    {
        return $this->canCreateModule($user, 'approvals');
    }

    public function decide(User $user, Approval $approval): bool
    {
        return ($user->hasPermission('approvals.approve') || $this->canManageModule($user, 'approvals'))
            && $approval->status === 'pending';
    }

    public function delete(User $user, Approval $approval): bool
    {
        return $this->canManageModule($user, 'approvals');
    }
}
