<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $group = $this->route('group', 'general');
        
        $rules = [];

        switch ($group) {
            case 'general':
                $rules = [
                    'system_name' => ['required', 'string', 'max:255'],
                    'system_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                    'language' => ['required', 'in:ar,en'],
                    'timezone' => ['required', 'string'],
                    'date_format' => ['required', 'string'],
                    'time_format' => ['required', 'string'],
                ];
                break;

            case 'email':
                $rules = [
                    'mail_mailer' => ['required', 'in:smtp,sendmail,mailgun,ses'],
                    'mail_host' => ['required', 'string', 'max:255'],
                    'mail_port' => ['required', 'integer', 'min:1', 'max:65535'],
                    'mail_username' => ['nullable', 'string', 'max:255'],
                    'mail_password' => ['nullable', 'string', 'max:255'],
                    'mail_encryption' => ['nullable', 'in:tls,ssl'],
                    'mail_from_address' => ['required', 'email', 'max:255'],
                    'mail_from_name' => ['required', 'string', 'max:255'],
                ];
                break;

            case 'support':
                $rules = [
                    'support_email' => ['nullable', 'email', 'max:255'],
                    'support_phone' => ['nullable', 'string', 'max:255'],
                    'support_whatsapp' => ['nullable', 'string', 'max:255'],
                    'support_address' => ['nullable', 'string', 'max:500'],
                    'support_website' => ['nullable', 'url', 'max:255'],
                ];
                break;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'system_name.required' => 'اسم النظام مطلوب',
            'system_logo.image' => 'اللوجو يجب أن يكون صورة',
            'system_logo.max' => 'حجم اللوجو يجب أن يكون أقل من 2 ميجابايت',
            'language.required' => 'اللغة مطلوبة',
            'timezone.required' => 'المنطقة الزمنية مطلوبة',
            'date_format.required' => 'تنسيق التاريخ مطلوب',
            'time_format.required' => 'تنسيق الوقت مطلوب',
            'mail_mailer.required' => 'نوع بريد الإرسال مطلوب',
            'mail_host.required' => 'خادم البريد مطلوب',
            'mail_port.required' => 'منفذ البريد مطلوب',
            'mail_from_address.required' => 'عنوان المرسل مطلوب',
            'mail_from_address.email' => 'عنوان المرسل يجب أن يكون بريد إلكتروني صحيح',
            'mail_from_name.required' => 'اسم المرسل مطلوب',
            'support_email.email' => 'البريد الإلكتروني للدعم الفني يجب أن يكون صحيح',
            'support_website.url' => 'موقع الدعم الفني يجب أن يكون رابط صحيح',
        ];
    }
}
