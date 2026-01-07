<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Task::class, \App\Policies\TaskPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Setting::class, \App\Policies\SettingPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Client::class, \App\Policies\ClientPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Project::class, \App\Policies\ProjectPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Approval::class, \App\Policies\ApprovalPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Invoice::class, \App\Policies\InvoicePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Expense::class, \App\Policies\ExpensePolicy::class);

        // Register Gates for permissions
        \Illuminate\Support\Facades\Gate::define('manage-roles-permissions', function ($user) {
            // Super admin has all permissions
            if ($user->hasRole('super_admin')) {
                return true;
            }
            return $user->hasPermission('manage-roles-permissions');
        });

        // Register Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PaymentCreated::class,
            \App\Listeners\UpdateInvoicePaidAmountAndStatus::class
        );
        
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\TaskCreated::class,
            \App\Listeners\SendTaskNotification::class
        );
        
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\TaskCommented::class,
            \App\Listeners\SendTaskCommentNotification::class
        );

        // تطبيق الإعدادات من قاعدة البيانات
        try {
            // المنطقة الزمنية
            $timezone = \App\Models\Setting::get('timezone', 'Asia/Riyadh');
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);

            // اللغة - سيتم التعامل معها في SetLocale Middleware
            // هنا نضع القيمة الافتراضية فقط
            config(['app.locale' => 'ar']);
            \App::setLocale('ar');

            // إعدادات البريد
            $mailSettings = \App\Models\Setting::getByGroup('email');
            if (!empty($mailSettings)) {
                config([
                    'mail.mailers.smtp.host' => $mailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $mailSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $mailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.encryption' => $mailSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'mail.from.address' => $mailSettings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $mailSettings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Exception $e) {
            // في حالة عدم وجود جدول settings، استخدم الإعدادات الافتراضية
            // لا حاجة لفعل شيء
        }
    }
}
