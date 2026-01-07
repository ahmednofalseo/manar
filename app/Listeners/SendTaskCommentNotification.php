<?php

namespace App\Listeners;

use App\Events\TaskCommented;
use App\Models\Notification;
use App\Models\Task;
use Illuminate\Support\Facades\App;

class SendTaskCommentNotification
{
    /**
     * Handle the event.
     */
    public function handle(TaskCommented $event): void
    {
        $task = $event->task;
        $comment = $event->comment;
        $commenterId = $event->userId;
        
        // إرسال إشعار للموظف المخصص (إذا لم يكن هو من علق)
        if ($task->assignee_id && $task->assignee_id !== $commenterId) {
            $assignee = $task->assignee;
            if ($assignee) {
                $locale = $this->getUserLocale($task->assignee_id);
                
                $title = $locale === 'ar'
                    ? 'تعليق جديد على المهمة'
                    : 'New Comment on Task';
                
                $message = $locale === 'ar'
                    ? "تم إضافة تعليق جديد على مهمتك: {$task->title}"
                    : "A new comment has been added to your task: {$task->title}";
                
                Notification::create([
                    'user_id' => $task->assignee_id,
                    'type' => 'task_comment',
                    'notifiable_type' => \App\Models\Task::class,
                    'notifiable_id' => $task->id,
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'comment_id' => $comment->id,
                        'commenter_id' => $commenterId,
                        'project_id' => $task->project_id,
                    ],
                ]);
            }
        }
        
        // إرسال إشعار لمنشئ المهمة (إذا لم يكن هو من علق ولم يكن هو الموظف المخصص)
        if ($task->created_by && $task->created_by !== $commenterId && $task->created_by !== $task->assignee_id) {
            $locale = $this->getUserLocale($task->created_by);
            
            $title = $locale === 'ar'
                ? 'تعليق جديد على المهمة'
                : 'New Comment on Task';
            
            $message = $locale === 'ar'
                ? "تم إضافة تعليق جديد على المهمة التي أنشأتها: {$task->title}"
                : "A new comment has been added to the task you created: {$task->title}";
            
            Notification::create([
                'user_id' => $task->created_by,
                'type' => 'task_comment',
                'notifiable_type' => \App\Models\Task::class,
                'notifiable_id' => $task->id,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'comment_id' => $comment->id,
                    'commenter_id' => $commenterId,
                    'project_id' => $task->project_id,
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
