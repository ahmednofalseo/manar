<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // يتم التحقق من الصلاحيات في Controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => 'required|in:approved,rejected',
            'reason' => 'required_if:action,rejected|nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'الإجراء مطلوب',
            'action.in' => 'الإجراء غير صحيح',
            'reason.required_if' => 'سبب الرفض مطلوب عند رفض المستند',
            'reason.max' => 'سبب الرفض يجب ألا يتجاوز 1000 حرف',
        ];
    }
}
