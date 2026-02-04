<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'type' => 'required|in:technical_report,quotation',
            'template_id' => 'nullable|exists:document_templates,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'service_id' => 'nullable|exists:services,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'total_price' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'نوع المستند مطلوب',
            'type.in' => 'نوع المستند غير صحيح',
            'template_id.exists' => 'القالب المحدد غير موجود',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'service_id.exists' => 'الخدمة المحددة غير موجودة',
            'title.required' => 'عنوان المستند مطلوب',
            'title.max' => 'عنوان المستند يجب ألا يتجاوز 255 حرف',
            'content.required' => 'محتوى المستند مطلوب',
            'total_price.numeric' => 'السعر الإجمالي يجب أن يكون رقماً',
            'total_price.min' => 'السعر الإجمالي يجب أن يكون أكبر من أو يساوي صفر',
            'expires_at.date' => 'تاريخ الانتهاء غير صحيح',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون بعد اليوم',
        ];
    }
}
