<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'voucher_number' => 'nullable|string|unique:expenses,voucher_number',
            'date' => 'required|date',
            'department' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'status' => 'nullable|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'voucher_number.unique' => __('Expense validation voucher number unique'),
            'date.required' => __('Expense validation date required'),
            'date.date' => __('Expense validation date invalid'),
            'department.required' => __('Expense validation department required'),
            'type.required' => __('Expense validation type required'),
            'description.required' => __('Expense validation description required'),
            'description.max' => __('Expense validation description max'),
            'amount.required' => __('Expense validation amount required'),
            'amount.numeric' => __('Expense validation amount numeric'),
            'amount.min' => __('Expense validation amount min'),
            'payment_method.required' => __('Expense validation payment method required'),
            'status.in' => __('Expense validation status invalid'),
            'attachments.*.file' => __('Expense validation attachment file'),
            'attachments.*.mimes' => __('Expense validation attachment mimes'),
            'attachments.*.max' => __('Expense validation attachment max'),
        ];
    }
}
