<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json, boolean
            $table->string('group')->default('general'); // general, email, appearance, support
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        $defaultSettings = [
            // General Settings
            ['key' => 'system_name', 'value' => 'المنار', 'type' => 'text', 'group' => 'general', 'description' => 'اسم النظام'],
            ['key' => 'system_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'description' => 'لوجو النظام'],
            ['key' => 'language', 'value' => 'ar', 'type' => 'text', 'group' => 'general', 'description' => 'اللغة الافتراضية'],
            ['key' => 'timezone', 'value' => 'Asia/Riyadh', 'type' => 'text', 'group' => 'general', 'description' => 'المنطقة الزمنية'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'text', 'group' => 'general', 'description' => 'تنسيق التاريخ'],
            ['key' => 'time_format', 'value' => 'H:i', 'type' => 'text', 'group' => 'general', 'description' => 'تنسيق الوقت'],
            
            // Email Settings
            ['key' => 'mail_mailer', 'value' => 'smtp', 'type' => 'text', 'group' => 'email', 'description' => 'نوع بريد الإرسال'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io', 'type' => 'text', 'group' => 'email', 'description' => 'خادم البريد'],
            ['key' => 'mail_port', 'value' => '2525', 'type' => 'text', 'group' => 'email', 'description' => 'منفذ البريد'],
            ['key' => 'mail_username', 'value' => null, 'type' => 'text', 'group' => 'email', 'description' => 'اسم المستخدم'],
            ['key' => 'mail_password', 'value' => null, 'type' => 'text', 'group' => 'email', 'description' => 'كلمة المرور'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'text', 'group' => 'email', 'description' => 'التشفير'],
            ['key' => 'mail_from_address', 'value' => 'noreply@manar.com', 'type' => 'text', 'group' => 'email', 'description' => 'عنوان المرسل'],
            ['key' => 'mail_from_name', 'value' => 'نظام المنار', 'type' => 'text', 'group' => 'email', 'description' => 'اسم المرسل'],
            
            // Support Settings
            ['key' => 'support_email', 'value' => 'support@manar.com', 'type' => 'text', 'group' => 'support', 'description' => 'البريد الإلكتروني للدعم الفني'],
            ['key' => 'support_phone', 'value' => '+966500000000', 'type' => 'text', 'group' => 'support', 'description' => 'رقم الهاتف للدعم الفني'],
            ['key' => 'support_whatsapp', 'value' => null, 'type' => 'text', 'group' => 'support', 'description' => 'رقم الواتساب للدعم الفني'],
            ['key' => 'support_address', 'value' => null, 'type' => 'text', 'group' => 'support', 'description' => 'عنوان الدعم الفني'],
            ['key' => 'support_website', 'value' => null, 'type' => 'text', 'group' => 'support', 'description' => 'موقع الدعم الفني'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
