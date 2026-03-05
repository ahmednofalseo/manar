<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    /**
     * Create admin user - works with different users table schemas.
     */
    public function run(): void
    {
        $email = 'admin@manar.com';

        if (User::where('email', $email)->exists()) {
            $this->command->info('مستخدم الأدمن موجود بالفعل.');
            return;
        }

        $columns = Schema::getColumnListing('users');
        $hasName = in_array('name', $columns);
        $hasFullname = in_array('fullname', $columns);
        $hasFirstName = in_array('first_name', $columns);
        $hasLastName = in_array('last_name', $columns);
        $hasPhone = in_array('phone', $columns);
        $hasMobile = in_array('mobile', $columns);
        $hasStatus = in_array('status', $columns);
        $hasIsBlock = in_array('is_block', $columns);
        $hasType = in_array('type', $columns);

        $data = [
            'email' => $email,
            'password' => Hash::make('admin123456'),
            'email_verified_at' => now(),
        ];

        if ($hasName) {
            $data['name'] = 'الأدمن العام';
        }
        if ($hasFullname) {
            $data['fullname'] = 'الأدمن العام';
        }
        if ($hasFirstName) {
            $data['first_name'] = 'الأدمن';
        }
        if ($hasLastName) {
            $data['last_name'] = 'العام';
        }
        if ($hasPhone) {
            $data['phone'] = '+966500000000';
        }
        if ($hasMobile) {
            $data['mobile'] = '+966500000000';
        }
        if ($hasStatus) {
            $data['status'] = 'active';
        }
        if ($hasIsBlock) {
            $data['is_block'] = 0;
        }
        if ($hasType) {
            $data['type'] = 'company';
        }

        // Use DB::table if schema has fullname (not name) to avoid model fillable mismatch
        if ($hasFullname && !$hasName) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('users')->insert($data);
            $admin = User::where('email', $email)->first();
        } else {
            $admin = User::create($data);
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole && $admin) {
            DB::table('role_user')->insert([
                'user_id' => $admin->id,
                'role_id' => $superAdminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('تم إنشاء مستخدم الأدمن بنجاح!');
        $this->command->info('البريد الإلكتروني: admin@manar.com');
        $this->command->info('كلمة المرور: admin123456');
    }
}
