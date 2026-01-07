<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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
        ]);

        // إنشاء مستخدم الأدمن العام
        $admin = User::create([
            'name' => 'الأدمن العام',
            'email' => 'admin@manar.com',
            'password' => Hash::make('admin123456'),
            'phone' => '+966500000000',
            'national_id' => '1234567890',
            'job_title' => 'مدير النظام',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // ربط الأدمن بدور super_admin
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $admin->roles()->attach($superAdminRole->id);
        }

        $this->command->info('تم إنشاء مستخدم الأدمن بنجاح!');
        $this->command->info('البريد الإلكتروني: admin@manar.com');
        $this->command->info('كلمة المرور: admin123456');
        $this->command->warn('⚠️ يرجى تغيير كلمة المرور بعد أول تسجيل دخول!');
    }
}
