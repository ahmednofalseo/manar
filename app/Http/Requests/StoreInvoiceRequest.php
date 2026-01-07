<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'project_id.required' => 'المشروع مطلوب',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'number.unique' => 'رقم الفاتورة مستخدم مسبقاً',
            'issue_date.required' => 'تاريخ الإصدار مطلوب',
            'issue_date.date' => 'تاريخ الإصدار غير صحيح',
            'due_date.required' => 'تاريخ الاستحقاق مطلوب',
            'due_date.date' => 'تاريخ الاستحقاق غير صحيح',
            'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون بعد أو يساوي تاريخ الإصدار',
            'total_amount.required' => 'المبلغ الإجمالي مطلوب',
            'total_amount.numeric' => 'المبلغ الإجمالي يجب أن يكون رقماً',
            'total_amount.min' => 'المبلغ الإجمالي يجب أن يكون أكبر من صفر',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
        ];
    }
}
