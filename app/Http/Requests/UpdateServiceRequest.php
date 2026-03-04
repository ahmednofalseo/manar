<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
        $serviceId = $this->route('service')->id ?? $this->route('service');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($serviceId),
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:service_categories,id',
            'parent_id' => [
                'nullable',
                'exists:services,id',
                Rule::notIn([$serviceId]), // لا يمكن أن تكون الخدمة رئيسية لنفسها
            ],
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
            'parent_id.not_in' => 'لا يمكن أن تكون الخدمة رئيسية لنفسها',
        ];
    }
}
