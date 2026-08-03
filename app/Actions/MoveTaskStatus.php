<?php

namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Support\TaskTransition;
use Illuminate\Auth\Access\AuthorizationException;
use RuntimeException;

class MoveTaskStatus
{
    /**
     * @throws RuntimeException|AuthorizationException
     */
    public function __invoke(Task $task, TaskStatus $to, User $actor): void
    {
        $permission = TaskTransition::permissionFor($task->status, $to);

        if (! $permission) {
            throw new RuntimeException('Move tasks one stage at a time.');
        }

        if (! $actor->can($permission)) {
            throw new AuthorizationException("Your role can't do that move.");
        }

        $from = $task->status;

        $task->update(['status' => $to]);

        $task->activities()->create([
            'user_id' => $actor->id,
            'body' => "Moved from {$from->label()} to {$to->label()}.",
            'created_at' => now(),
        ]);
    }
}
