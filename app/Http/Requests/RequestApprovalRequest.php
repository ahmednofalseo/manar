<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('request', \App\Models\Approval::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'approvable_type' => ['required', 'string', Rule::in(['App\Models\ProjectAttachment', 'App\Models\ProjectStage'])],
            'approvable_id' => ['required', 'integer'],
            'stage_key' => ['required', 'string', Rule::in(\App\Enums\ProjectStageKey::all())],
            'manager_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'المشروع مطلوب',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'approvable_type.required' => 'نوع العنصر مطلوب',
            'approvable_type.in' => 'نوع العنصر غير صالح',
            'approvable_id.required' => 'معرف العنصر مطلوب',
            'stage_key.required' => 'المرحلة مطلوبة',
            'stage_key.in' => 'المرحلة غير صالحة',
            'manager_note.max' => 'ملاحظة المدير يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
