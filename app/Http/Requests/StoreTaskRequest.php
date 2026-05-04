<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', \App\Models\Task::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();
        if (isset($data['project_stage_id']) && $data['project_stage_id'] === '') {
            $data['project_stage_id'] = null;
        }
        foreach (['title_en', 'description_en', 'manager_notes_en'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'project_stage_id' => [
                'nullable',
                'exists:project_stages,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $project = \App\Models\Project::find($this->project_id);
                        if ($project && ! $project->projectStages()->where('id', $value)->exists()) {
                            $fail(__('Task validation stage not belongs project'));
                        }
                    }
                },
            ],
            'assignee_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'manager_notes' => ['nullable', 'string'],
            'manager_notes_en' => ['nullable', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'], // 10MB max per file
            'status' => ['nullable', 'in:new,in_progress,done,rejected'],
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
            'project_id.required' => __('Task validation project required'),
            'project_id.exists' => __('Task validation project invalid'),
            'project_stage_id.exists' => __('Task validation stage invalid'),
            'assignee_id.required' => __('Task validation assignee required'),
            'assignee_id.exists' => __('Task validation assignee invalid'),
            'title.required' => __('Task validation title required'),
            'title.max' => __('Task validation title max'),
            'priority.in' => __('Task validation priority invalid'),
            'due_date.after_or_equal' => __('Task validation due after start'),
            'progress.min' => __('Task validation progress min'),
            'progress.max' => __('Task validation progress max'),
            'status.in' => __('Task validation status invalid'),
        ];
    }
}
