<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecideApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('decide', $this->route('approval'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', Rule::in(['approve', 'reject'])],
            'note' => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'القرار مطلوب',
            'decision.in' => 'القرار يجب أن يكون موافقة أو رفض',
            'note.required_if' => 'سبب الرفض مطلوب',
            'note.max' => 'الملاحظة يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
