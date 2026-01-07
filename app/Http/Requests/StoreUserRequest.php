<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('users.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'national_id' => ['nullable', 'string', 'max:20', 'unique:users,national_id'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'practice_license_no' => ['nullable', 'string', 'max:255'],
            'practice_license_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'engineer_rank_expiry' => ['nullable', 'date'],
            'status' => ['required', 'in:active,suspended'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'national_id.unique' => 'رقم الهوية مستخدم بالفعل',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'avatar.image' => 'يجب أن يكون الملف صورة',
            'avatar.mimes' => 'نوع الصورة يجب أن يكون: jpeg, jpg, png',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
            'roles.array' => 'الأدوار يجب أن تكون مصفوفة',
            'roles.min' => 'يجب اختيار دور واحد على الأقل',
            'roles.*.exists' => 'أحد الأدوار المختارة غير موجود',
        ];
    }
}


