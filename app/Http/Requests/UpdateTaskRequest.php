<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $taskId = $this->route('id');
        if (!$taskId) {
            return false;
        }
        
        $task = \App\Models\Task::findOrFail($taskId);
        return auth()->user()->can('update', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $taskId = $this->route('task')?->id ?? $this->route('id');
        
        return [
            'project_id' => ['sometimes', 'required', 'exists:projects,id'],
            'project_stage_id' => [
                'nullable',
                'exists:project_stages,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->project_id) {
                        $project = \App\Models\Project::find($this->project_id);
                        if ($project && !$project->projectStages()->where('id', $value)->exists()) {
                            $fail('المرحلة المحددة غير مرتبطة بهذا المشروع.');
                        }
                    }
                },
            ],
            'assignee_id' => ['sometimes', 'required', 'exists:users,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'manager_notes' => ['nullable', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'completion_notes' => ['nullable', 'string'],
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
            'project_id.required' => 'المشروع مطلوب',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'project_stage_id.exists' => 'المرحلة المحددة غير موجودة',
            'assignee_id.required' => 'الموظف المسند إليه مطلوب',
            'assignee_id.exists' => 'الموظف المحدد غير موجود',
            'title.required' => 'عنوان المهمة مطلوب',
            'title.max' => 'عنوان المهمة يجب أن يكون أقل من 255 حرف',
            'priority.in' => 'الأولوية يجب أن تكون: منخفضة، متوسطة، أو عالية',
            'due_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء أو مساوياً له',
            'progress.min' => 'نسبة الإنجاز يجب أن تكون بين 0 و 100',
            'progress.max' => 'نسبة الإنجاز يجب أن تكون بين 0 و 100',
        ];
    }
}
