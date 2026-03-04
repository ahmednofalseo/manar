<?php

namespace App\Events;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCommented
{
    use Dispatchable, SerializesModels;

    public Task $task;
    public TaskNote $comment;
    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, TaskNote $comment, int $userId)
    {
        $this->task = $task;
        $this->comment = $comment;
        $this->userId = $userId;
    }
}
