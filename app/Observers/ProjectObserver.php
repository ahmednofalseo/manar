<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Conversation;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // إغلاق الشات تلقائياً عند انتهاء المشروع
        if ($project->isDirty('status') && $project->status === 'مكتمل') {
            $conversation = Conversation::where('type', 'project')
                ->where('project_id', $project->id)
                ->first();
            
            if ($conversation && !$conversation->is_closed) {
                $conversation->close();
            }
        }
        
        // فتح الشات إذا تم استئناف المشروع
        if ($project->isDirty('status') && $project->status !== 'مكتمل' && $project->getOriginal('status') === 'مكتمل') {
            $conversation = Conversation::where('type', 'project')
                ->where('project_id', $project->id)
                ->first();
            
            if ($conversation && $conversation->is_closed) {
                $conversation->open();
            }
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        // إغلاق الشات عند حذف المشروع
        $conversation = Conversation::where('type', 'project')
            ->where('project_id', $project->id)
            ->first();
        
        if ($conversation) {
            $conversation->close();
        }
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
