<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('workflow-templates.create') || 
               $this->user()->hasPermission('workflow-templates.manage') ||
               $this->user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.order' => 'required|integer|min:0',
            'steps.*.department' => 'required|string|in:معماري,إنشائي,كهربائي,ميكانيكي,مساحي,دفاع_مدني,بلدي,أخرى',
            'steps.*.default_duration_days' => 'required|integer|min:1',
            'steps.*.expected_outputs' => 'nullable|array',
            'steps.*.dependencies' => 'nullable|array',
            'steps.*.is_parallel' => 'boolean',
            'steps.*.is_required' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'الخدمة مطلوبة',
            'service_id.exists' => 'الخدمة المحددة غير موجودة',
            'name.required' => 'اسم القالب مطلوب',
            'steps.required' => 'يجب إضافة خطوة واحدة على الأقل',
            'steps.*.name.required' => 'اسم الخطوة مطلوب',
            'steps.*.order.required' => 'ترتيب الخطوة مطلوب',
            'steps.*.department.required' => 'القسم مطلوب',
            'steps.*.default_duration_days.required' => 'المدة الافتراضية مطلوبة',
        ];
    }
}
