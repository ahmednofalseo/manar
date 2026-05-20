<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class InvoicePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'financials');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->canViewModule($user, 'financials');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'financials');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $this->canUpdateModule($user, 'financials');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->canDeleteModule($user, 'financials');
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return $this->canManageModule($user, 'financials');
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $this->canManageModule($user, 'financials');
    }
}
