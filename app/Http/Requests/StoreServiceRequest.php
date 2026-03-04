<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:service_categories,id',
            'parent_id' => 'nullable|exists:services,id',
            'is_custom' => 'boolean',
            'has_sub_services' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الخدمة مطلوب',
            'slug.unique' => 'المعرف الفريد مستخدم بالفعل',
            'category_id.exists' => 'الفئة المحددة غير موجودة',
            'parent_id.exists' => 'الخدمة الرئيسية المحددة غير موجودة',
        ];
    }
}
