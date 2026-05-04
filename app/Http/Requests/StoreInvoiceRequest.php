<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // سيتم التحقق من الصلاحيات في Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'number' => 'nullable|string|unique:invoices,number',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,transfer,check,electronic',
            'notes' => 'nullable|string|max:1000',
            'installments_count' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => __('Invoice validation project required'),
            'project_id.exists' => __('Invoice validation project invalid'),
            'client_id.exists' => __('Invoice validation client invalid'),
            'number.unique' => __('Invoice validation number unique'),
            'issue_date.required' => __('Invoice validation issue date required'),
            'issue_date.date' => __('Invoice validation issue date invalid'),
            'due_date.required' => __('Invoice validation due date required'),
            'due_date.date' => __('Invoice validation due date invalid'),
            'due_date.after_or_equal' => __('Invoice validation due after issue'),
            'total_amount.required' => __('Invoice validation total required'),
            'total_amount.numeric' => __('Invoice validation total numeric'),
            'total_amount.min' => __('Invoice validation total min'),
            'payment_method.in' => __('Invoice validation payment method invalid'),
        ];
    }
}
