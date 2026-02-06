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
        $rules = [
            'type' => 'required|in:technical_report,quotation',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'service_id' => 'nullable|exists:services,id',
            'title' => 'required|string|max:255',
            'status' => 'nullable|in:draft,sent,accepted,expired',
        ];
        
        // For quotations, different rules
        if ($this->input('type') === 'quotation') {
            $rules['issue_date'] = 'required|date';
            $rules['valid_until'] = 'nullable|date|after_or_equal:issue_date';
            $rules['items'] = 'required'; // JSON string or array
            $rules['client_id'] = 'required|exists:clients,id'; // Required for quotations
            $rules['discount_type'] = 'nullable|in:amount,percent';
            $rules['discount_value'] = 'nullable|numeric|min:0';
            $rules['vat_percent'] = 'nullable|numeric|min:0|max:100';
            $rules['terms_html'] = 'nullable|string';
            $rules['notes_internal'] = 'nullable|string';
            $rules['template_id'] = 'nullable'; // No template for quotations
            $rules['content'] = 'nullable|string'; // Not required for quotations
            $rules['title'] = 'nullable|string|max:255'; // Title can be auto-generated
        } else {
            // For technical reports
            $rules['template_id'] = 'nullable|exists:document_templates,id';
            $rules['content'] = 'required|string';
            $rules['total_price'] = 'nullable|numeric|min:0';
            $rules['expires_at'] = 'nullable|date|after:today';
        }
        
        return $rules;
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
