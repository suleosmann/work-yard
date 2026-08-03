<?php

namespace App\Support;

use App\Enums\TaskStatus;

class TaskTransition
{
    /**
     * Allowed forward/backward moves and the permission each one requires.
     *
     * @return array<string, string>
     */
    public static function map(): array
    {
        return [
            'pending->in_progress' => Permission::ADVANCE_TASK_STATUS,
            'in_progress->qa' => Permission::ADVANCE_TASK_STATUS,
            'qa->done' => Permission::QA_DECIDE,
            'qa->in_progress' => Permission::QA_DECIDE,
        ];
    }

    public static function permissionFor(TaskStatus $from, TaskStatus $to): ?string
    {
        return self::map()["{$from->value}->{$to->value}"] ?? null;
    }
}
