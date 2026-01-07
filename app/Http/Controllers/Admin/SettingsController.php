<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Setting::class);

        $group = $request->get('group', 'general');
        
        $settings = Setting::where('group', $group)->get()->pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings', 'group'));
    }

    /**
     * Update settings.
     */
    public function update(UpdateSettingsRequest $request, string $group)
    {
        Gate::authorize('update', Setting::class);

        $data = $request->validated();

        // معالجة رفع اللوجو
        if ($request->hasFile('system_logo')) {
            // حذف اللوجو القديم إن وجد
            $oldLogo = Setting::get('system_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // رفع اللوجو الجديد
            $logoPath = $request->file('system_logo')->store('settings', 'public');
            $data['system_logo'] = $logoPath;
        } else {
            // إذا لم يتم رفع لوجو جديد، احتفظ بالقيمة القديمة
            unset($data['system_logo']);
        }

        // حفظ الإعدادات
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // تحديث إعدادات البريد في ملف .env إذا كانت مجموعة email
        if ($group === 'email') {
            $this->updateMailConfig($data);
        }

        // تحديث إعدادات التطبيق في runtime
        $this->updateAppConfig();

        // مسح جميع الكاشات للتأكد من تطبيق التغييرات فوراً
        Cache::flush();
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * تحديث إعدادات البريد في ملف .env
     */
    private function updateMailConfig(array $data)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);
        
        $mailSettings = [
            'MAIL_MAILER' => $data['mail_mailer'] ?? 'smtp',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '2525',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $data['mail_encryption'] ?? 'tls',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? '',
        ];

        foreach ($mailSettings as $key => $value) {
            // استبدال القيمة الموجودة أو إضافتها إذا لم تكن موجودة
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * تحديث إعدادات التطبيق في runtime
     */
    private function updateAppConfig()
    {
        // تحديث المنطقة الزمنية
        $timezone = Setting::get('timezone', 'Asia/Riyadh');
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        // تحديث اللغة
        $language = Setting::get('language', 'ar');
        config(['app.locale' => $language]);
        \App::setLocale($language);
    }

    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        Gate::authorize('update', Setting::class);

        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            // تحديث إعدادات البريد مؤقتاً
            config([
                'mail.mailers.smtp.host' => Setting::get('mail_host'),
                'mail.mailers.smtp.port' => Setting::get('mail_port'),
                'mail.mailers.smtp.username' => Setting::get('mail_username'),
                'mail.mailers.smtp.password' => Setting::get('mail_password'),
                'mail.mailers.smtp.encryption' => Setting::get('mail_encryption'),
                'mail.from.address' => Setting::get('mail_from_address'),
                'mail.from.name' => Setting::get('mail_from_name'),
            ]);

            \Mail::raw('هذه رسالة تجريبية من نظام المنار', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('رسالة تجريبية - نظام المنار');
            });

            return back()->with('success', 'تم إرسال رسالة تجريبية بنجاح إلى ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إرسال الرسالة: ' . $e->getMessage());
        }
    }
}
