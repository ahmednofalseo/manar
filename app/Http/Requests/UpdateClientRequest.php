<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('id') ? \App\Models\Client::findOrFail($this->route('id')) : \App\Models\Client::class);
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
            'type' => ['required', 'in:individual,company,government'],
            'national_id_or_cr' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
            'notes_internal' => ['nullable', 'string'],
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
            'name.required' => 'اسم العميل مطلوب',
            'name.string' => 'اسم العميل يجب أن يكون نص',
            'name.max' => 'اسم العميل يجب ألا يتجاوز 255 حرف',
            'type.required' => 'نوع العميل مطلوب',
            'type.in' => 'نوع العميل غير صحيح',
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.string' => 'رقم الجوال يجب أن يكون نص',
            'phone.max' => 'رقم الجوال يجب ألا يتجاوز 20 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'city.required' => 'المدينة مطلوبة',
            'city.string' => 'المدينة يجب أن تكون نص',
            'city.max' => 'المدينة يجب ألا تتجاوز 100 حرف',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
        ];
    }
}
