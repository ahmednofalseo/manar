<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowTemplateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Decode expected_outputs from JSON string
        $steps = $this->input('steps', []);
        foreach ($steps as $index => $step) {
            if (isset($step['expected_outputs']) && is_string($step['expected_outputs'])) {
                $decoded = json_decode($step['expected_outputs'], true);
                $steps[$index]['expected_outputs'] = is_array($decoded) ? $decoded : [];
            }
            // Ensure default_duration_days is integer
            if (isset($step['default_duration_days'])) {
                $steps[$index]['default_duration_days'] = (int) $step['default_duration_days'];
            }
        }
        $this->merge(['steps' => $steps]);

        // Set defaults for checkboxes when not sent (unchecked = not in request)
        $merge = [];
        if (! $this->has('is_default')) {
            $merge['is_default'] = false;
        }
        if (! $this->has('is_active')) {
            $merge['is_active'] = false; // unchecked = not active
        }
        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $steps = $this->input('steps', []);
        foreach ($steps as $index => $step) {
            if (isset($step['expected_outputs']) && is_string($step['expected_outputs'])) {
                $decoded = json_decode($step['expected_outputs'], true);
                $steps[$index]['expected_outputs'] = is_array($decoded) ? $decoded : null;
            }
        }
        $this->merge(['steps' => $steps]);
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
