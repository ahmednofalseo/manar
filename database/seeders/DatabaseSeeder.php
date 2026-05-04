<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // أولاً: إنشاء الأدوار والصلاحيات
        $this->call([
            RolesAndPermissionsSeeder::class,
            DocumentTemplatesSeeder::class,
            ProjectSettingsSeeder::class,
        ]);

        // أدمن افتراضي فقط إذا لم يكن هناك مستخدم بهذا البريد (لا نستبدل الأدمن الحالي)
        $adminEmail = 'admin@manar.com';
        $admin = User::where('email', $adminEmail)->first();

        if (! $admin) {
            $admin = User::create([
                'name' => 'الأدمن العام',
                'email' => $adminEmail,
                'password' => Hash::make('admin123456'),
                'phone' => '+966500000000',
                'national_id' => '1234567890',
                'job_title' => 'مدير النظام',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $this->command->info('تم إنشاء مستخدم الأدمن الافتراضي.');
            $this->command->info('البريد: '.$adminEmail.' | كلمة المرور: admin123456');
            $this->command->warn('⚠️ غيّر كلمة المرور بعد أول تسجيل دخول!');
        } else {
            $this->command->info('تخطي إنشاء الأدمن: يوجد مستخدم بالبريد '.$adminEmail.' (يُحتفظ بالحساب الحالي).');
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole && $admin && ! $admin->roles()->where('roles.id', $superAdminRole->id)->exists()) {
            $admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
            $this->command->info('تم ربط الحساب بدور super_admin (إن لم يكن مربوطًا).');
        }
    }
}
