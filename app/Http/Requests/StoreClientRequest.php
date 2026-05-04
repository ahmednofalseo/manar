<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Client::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,company,government'],
            'national_id_or_cr' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
            'notes_internal' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Client validation name required'),
            'name.string' => __('Client validation name string'),
            'name.max' => __('Client validation name max'),
            'type.required' => __('Client validation type required'),
            'type.in' => __('Client validation type in'),
            'phone.required' => __('Client validation phone required'),
            'phone.string' => __('Client validation phone string'),
            'phone.max' => __('Client validation phone max'),
            'email.email' => __('Client validation email email'),
            'email.max' => __('Client validation email max'),
            'city.required' => __('Client validation city required'),
            'city.string' => __('Client validation city string'),
            'city.max' => __('Client validation city max'),
            'status.required' => __('Client validation status required'),
            'status.in' => __('Client validation status in'),
        ];
    }
}
