<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    public function created(Task $task)
    {
        $this->log($task, 'created');
    }

    public function updated(Task $task)
    {
        $changes = [
            'before' => $task->getOriginal(),
            'after' => $task->getChanges(),
        ];

        $this->log($task, 'updated', $changes);
    }

    public function deleted(Task $task)
    {
        $changes = [
            'before' => $task->getOriginal(),
        ];

        $this->log($task, 'deleted', $changes);
    }

    protected function log(Task $task, string $action, array $changes = null)
    {
        $task->activityLogs()->create([
            'action' => $action,
            'changes' => $changes,
            'user_id' => Auth::id(), // Will be null for unauthenticated requests
        ]);
    }
}
