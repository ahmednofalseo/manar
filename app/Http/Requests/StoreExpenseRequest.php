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
            'voucher_number.unique' => 'رقم السند مستخدم مسبقاً',
            'date.required' => 'التاريخ مطلوب',
            'date.date' => 'التاريخ غير صحيح',
            'department.required' => 'القسم مطلوب',
            'type.required' => 'نوع المصروف مطلوب',
            'description.required' => 'الوصف مطلوب',
            'description.max' => 'الوصف يجب أن يكون أقل من 1000 حرف',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'attachments.*.file' => 'الملف المرفق غير صحيح',
            'attachments.*.mimes' => 'نوع الملف يجب أن يكون: pdf, jpg, jpeg, png',
            'attachments.*.max' => 'حجم الملف يجب أن يكون أقل من 10MB',
        ];
    }
}
