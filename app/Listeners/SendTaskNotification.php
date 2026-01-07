<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\Notification;
use Illuminate\Support\Facades\App;

class SendTaskNotification
{
    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;
        
        // إرسال إشعار للموظف المخصص
        if ($task->assignee_id) {
            $assignee = $task->assignee;
            if (!$assignee) {
                return;
            }
            
            // الحصول على لغة المستخدم من الإعدادات
            try {
                $userLocale = \App\Models\Setting::where('key', 'language')
                    ->where('value', session('locale', 'ar'))
                    ->first()?->value ?? 'ar';
            } catch (\Exception $e) {
                $userLocale = 'ar';
            }
            
            // استخدام locale المستخدم المستهدف
            $locale = $userLocale;
            
            $title = $locale === 'ar' 
                ? 'مهمة جديدة مخصصة لك'
                : 'New Task Assigned to You';
            
            $message = $locale === 'ar'
                ? "تم تعيين مهمة جديدة لك: {$task->title}"
                : "A new task has been assigned to you: {$task->title}";
            
            if ($task->project) {
                $message .= $locale === 'ar'
                    ? " في مشروع: {$task->project->name}"
                    : " in project: {$task->project->name}";
            }
            
            Notification::create([
                'user_id' => $task->assignee_id,
                'type' => 'task_assigned',
                'notifiable_type' => \App\Models\Task::class,
                'notifiable_id' => $task->id,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'project_id' => $task->project_id,
                    'project_name' => $task->project->name ?? null,
                    'created_by' => $task->created_by,
                ],
            ]);
        }
    }
    
    /**
     * Get user locale preference
     */
    private function getUserLocale(int $userId): string
    {
        try {
            $userLocale = \App\Models\Setting::where('key', 'language')
                ->first()?->value ?? 'ar';
            return $userLocale;
        } catch (\Exception $e) {
            return 'ar';
        }
    }
}
