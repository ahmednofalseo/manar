<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Policies\Concerns\ChecksModulePermissions;

class WorkflowTemplatePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'workflow-templates');
    }

    public function view(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $this->canViewModule($user, 'workflow-templates');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'workflow-templates');
    }

    public function update(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $this->canUpdateModule($user, 'workflow-templates');
    }

    public function delete(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $this->canDeleteModule($user, 'workflow-templates');
    }
}
