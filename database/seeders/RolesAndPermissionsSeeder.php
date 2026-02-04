<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get Roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'الأدمن العام',
                'description' => 'صلاحيات كاملة على جميع الوحدات'
            ]
        );

        $projectManager = Role::firstOrCreate(
            ['name' => 'project_manager'],
            [
                'display_name' => 'مدير المشروع',
                'description' => 'إدارة المشاريع والمهام والفريق'
            ]
        );

        $engineer = Role::firstOrCreate(
            ['name' => 'engineer'],
            [
                'display_name' => 'مهندس/فني',
                'description' => 'إدارة المهام المسندة والمشاريع المرتبطة'
            ]
        );

        $adminStaff = Role::firstOrCreate(
            ['name' => 'admin_staff'],
            [
                'display_name' => 'الإداري',
                'description' => 'إدارة العملاء والفواتير والمصروفات'
            ]
        );

        // Create Permissions
        $permissions = [
            // Users Permissions
            ['name' => 'users.view', 'display_name' => 'عرض المستخدمين', 'group' => 'Users'],
            ['name' => 'users.create', 'display_name' => 'إنشاء مستخدم', 'group' => 'Users'],
            ['name' => 'users.edit', 'display_name' => 'تعديل مستخدم', 'group' => 'Users'],
            ['name' => 'users.delete', 'display_name' => 'حذف مستخدم', 'group' => 'Users'],
            ['name' => 'users.manage', 'display_name' => 'إدارة كاملة للمستخدمين', 'group' => 'Users'],

            // Projects Permissions
            ['name' => 'projects.view', 'display_name' => 'عرض المشاريع', 'group' => 'Projects'],
            ['name' => 'projects.create', 'display_name' => 'إنشاء مشروع', 'group' => 'Projects'],
            ['name' => 'projects.edit', 'display_name' => 'تعديل مشروع', 'group' => 'Projects'],
            ['name' => 'projects.delete', 'display_name' => 'حذف مشروع', 'group' => 'Projects'],
            ['name' => 'projects.manage', 'display_name' => 'إدارة كاملة للمشاريع', 'group' => 'Projects'],

            // Tasks Permissions
            ['name' => 'tasks.view', 'display_name' => 'عرض المهام', 'group' => 'Tasks'],
            ['name' => 'tasks.create', 'display_name' => 'إنشاء مهمة', 'group' => 'Tasks'],
            ['name' => 'tasks.edit', 'display_name' => 'تعديل مهمة', 'group' => 'Tasks'],
            ['name' => 'tasks.delete', 'display_name' => 'حذف مهمة', 'group' => 'Tasks'],
            ['name' => 'tasks.manage', 'display_name' => 'إدارة كاملة للمهام', 'group' => 'Tasks'],

            // Financials Permissions
            ['name' => 'financials.view', 'display_name' => 'عرض الفواتير', 'group' => 'Financials'],
            ['name' => 'financials.create', 'display_name' => 'إنشاء فاتورة', 'group' => 'Financials'],
            ['name' => 'financials.edit', 'display_name' => 'تعديل فاتورة', 'group' => 'Financials'],
            ['name' => 'financials.delete', 'display_name' => 'حذف فاتورة', 'group' => 'Financials'],
            ['name' => 'financials.manage', 'display_name' => 'إدارة كاملة للفواتير', 'group' => 'Financials'],

            // Expenses Permissions
            ['name' => 'expenses.view', 'display_name' => 'عرض المصروفات', 'group' => 'Expenses'],
            ['name' => 'expenses.create', 'display_name' => 'إنشاء مصروف', 'group' => 'Expenses'],
            ['name' => 'expenses.approve', 'display_name' => 'اعتماد مصروف', 'group' => 'Expenses'],
            ['name' => 'expenses.manage', 'display_name' => 'إدارة كاملة للمصروفات', 'group' => 'Expenses'],

            // Clients Permissions
            ['name' => 'clients.view', 'display_name' => 'عرض العملاء', 'group' => 'Clients'],
            ['name' => 'clients.create', 'display_name' => 'إنشاء عميل', 'group' => 'Clients'],
            ['name' => 'clients.edit', 'display_name' => 'تعديل عميل', 'group' => 'Clients'],
            ['name' => 'clients.manage', 'display_name' => 'إدارة كاملة للعملاء', 'group' => 'Clients'],

            // Settings Permissions
            ['name' => 'settings.view', 'display_name' => 'عرض الإعدادات', 'group' => 'Settings'],
            ['name' => 'settings.manage', 'display_name' => 'إدارة كاملة للإعدادات', 'group' => 'Settings'],

            // Approvals Permissions
            ['name' => 'approvals.view', 'display_name' => 'عرض الموافقات', 'group' => 'Approvals'],
            ['name' => 'approvals.create', 'display_name' => 'إنشاء طلب موافقة', 'group' => 'Approvals'],
            ['name' => 'approvals.approve', 'display_name' => 'الموافقة على الطلبات', 'group' => 'Approvals'],
            ['name' => 'approvals.manage', 'display_name' => 'إدارة كاملة للموافقات', 'group' => 'Approvals'],

            // Services Permissions
            ['name' => 'services.view', 'display_name' => 'عرض الخدمات', 'group' => 'Services'],
            ['name' => 'services.create', 'display_name' => 'إنشاء خدمة', 'group' => 'Services'],
            ['name' => 'services.edit', 'display_name' => 'تعديل خدمة', 'group' => 'Services'],
            ['name' => 'services.delete', 'display_name' => 'حذف خدمة', 'group' => 'Services'],
            ['name' => 'services.manage', 'display_name' => 'إدارة كاملة للخدمات', 'group' => 'Services'],

            // Workflow Templates Permissions
            ['name' => 'workflow-templates.view', 'display_name' => 'عرض قوالب المسارات', 'group' => 'Workflows'],
            ['name' => 'workflow-templates.create', 'display_name' => 'إنشاء قالب مسار', 'group' => 'Workflows'],
            ['name' => 'workflow-templates.edit', 'display_name' => 'تعديل قالب مسار', 'group' => 'Workflows'],
            ['name' => 'workflow-templates.delete', 'display_name' => 'حذف قالب مسار', 'group' => 'Workflows'],
            ['name' => 'workflow-templates.manage', 'display_name' => 'إدارة كاملة لقوالب المسارات', 'group' => 'Workflows'],

            // Documents Permissions
            ['name' => 'documents.view', 'display_name' => 'عرض المستندات', 'group' => 'Documents'],
            ['name' => 'documents.create', 'display_name' => 'إنشاء مستند', 'group' => 'Documents'],
            ['name' => 'documents.edit', 'display_name' => 'تعديل مستند', 'group' => 'Documents'],
            ['name' => 'documents.delete', 'display_name' => 'حذف مستند', 'group' => 'Documents'],
            ['name' => 'documents.submit', 'display_name' => 'إرسال مستند', 'group' => 'Documents'],
            ['name' => 'documents.approve', 'display_name' => 'اعتماد مستند', 'group' => 'Documents'],
            ['name' => 'documents.manage', 'display_name' => 'إدارة كاملة للمستندات', 'group' => 'Documents'],

            // Roles & Permissions Management
            ['name' => 'manage-roles-permissions', 'display_name' => 'إدارة الأدوار والصلاحيات', 'group' => 'Settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Assign all permissions to super_admin
        $superAdmin->permissions()->syncWithoutDetaching(Permission::all()->pluck('id'));

        // Assign permissions to project_manager
        $projectManager->permissions()->syncWithoutDetaching(Permission::whereIn('name', [
            'projects.manage',
            'tasks.manage',
            'clients.view',
            'financials.view',
            'expenses.view',
            'services.view',
            'workflow-templates.view',
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.submit',
        ])->pluck('id'));

        // Assign permissions to engineer
        $engineer->permissions()->syncWithoutDetaching(Permission::whereIn('name', [
            'projects.view',
            'projects.create',
            'tasks.view',
            'tasks.edit',
            'tasks.create',
            'documents.view',
            'documents.create', // يمكن للمهندس إنشاء مسودة فقط
        ])->pluck('id'));

        // Assign permissions to admin_staff
        $adminStaff->permissions()->syncWithoutDetaching(Permission::whereIn('name', [
            'clients.manage',
            'financials.manage',
            'expenses.view',
            'expenses.create',
            'projects.view',
            'documents.view',
            'documents.create', // عروض الأسعار فقط
            'documents.edit', // عروض الأسعار فقط
        ])->pluck('id'));
    }
}
