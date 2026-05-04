<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // أي مستخدم مسجل دخول يمكنه تعديل حسابه
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required_with:password', 'string'],
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
            'name.required' => __('Profile validation name required'),
            'email.required' => __('Profile validation email required'),
            'email.email' => __('Profile validation email invalid'),
            'email.unique' => __('Profile validation email unique'),
            'password.min' => __('Profile validation password min'),
            'password.confirmed' => __('Profile validation password confirmed'),
            'current_password.required_with' => __('Profile validation current password required with'),
            'avatar.image' => __('Profile validation avatar image'),
            'avatar.max' => __('Profile validation avatar max'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // التحقق من كلمة المرور الحالية
            if ($this->filled('password') && $this->filled('current_password')) {
                if (! Hash::check($this->current_password, $this->user()->password)) {
                    $validator->errors()->add('current_password', __('Profile validation current password incorrect'));
                }
            }
        });
    }
}
