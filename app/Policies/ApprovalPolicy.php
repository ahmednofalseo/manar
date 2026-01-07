<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_staff', 'project_manager', 'engineer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Approval $approval): bool
    {
        // Admin & PM: يمكنهم رؤية كل شيء
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff') || $user->hasRole('project_manager')) {
            return true;
        }

        // Engineer: يمكنه رؤية الموافقات المتعلقة بمشاريعه
        if ($user->hasRole('engineer')) {
            return $approval->project->teamUsers->contains($user->id) 
                || $approval->project->project_manager_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can request approval.
     */
    public function request(User $user, ?Approval $approval = null): bool
    {
        // Admin & PM: يمكنهم طلب الموافقة
        return $user->hasRole('super_admin') 
            || $user->hasRole('admin_staff') 
            || $user->hasRole('project_manager');
    }

    /**
     * Determine whether the user can decide on approval.
     */
    public function decide(User $user, Approval $approval): bool
    {
        // إذا كان العميل مرتبط بالموافقة، فقط العميل يمكنه الموافقة/الرفض
        if ($approval->client_id) {
            // في حالة العميل، نحتاج للتحقق من أن المستخدم هو العميل
            // يمكن إضافة علاقة user_id في جدول clients إذا لزم الأمر
            // حالياً، سنسمح للمدير أيضاً بالموافقة/الرفض
            return $user->hasRole('super_admin') 
                || $user->hasRole('admin_staff') 
                || $user->hasRole('project_manager');
        }

        // Admin & PM: يمكنهم الموافقة/الرفض
        return $user->hasRole('super_admin') 
            || $user->hasRole('admin_staff') 
            || $user->hasRole('project_manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Approval $approval): bool
    {
        // فقط super_admin يمكنه الحذف
        return $user->hasRole('super_admin');
    }
}
