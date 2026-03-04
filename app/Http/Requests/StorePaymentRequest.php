<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'payment_no' => 'nullable|string|unique:payments,payment_no',
            'amount' => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'status' => 'required|in:paid,pending',
            'method' => 'required|in:cash,transfer,check,electronic',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_no.unique' => 'رقم الدفعة مستخدم مسبقاً',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'paid_at.required' => 'تاريخ الدفعة مطلوب',
            'paid_at.date' => 'تاريخ الدفعة غير صحيح',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'method.required' => 'طريقة الدفع مطلوبة',
            'method.in' => 'طريقة الدفع غير صحيحة',
            'attachment.file' => 'الملف المرفق غير صحيح',
            'attachment.mimes' => 'نوع الملف يجب أن يكون: pdf, jpg, jpeg, png',
            'attachment.max' => 'حجم الملف يجب أن يكون أقل من 10MB',
        ];
    }
}
