<?php

namespace App\Support;

class Permission
{
    const CREATE_PROJECTS = 'create projects';

    const EDIT_PROJECTS = 'edit projects';

    const CREATE_TASKS = 'create tasks';

    const ASSIGN_TASKS = 'assign tasks';

    const EDIT_TASK_FIELDS = 'edit task fields';

    const DELETE_TASKS = 'delete tasks';

    const ADVANCE_TASK_STATUS = 'advance task status';

    const QA_DECIDE = 'qa decide';

    const MANAGE_RESOURCES = 'manage resources';

    const ADD_COMMENTS = 'add comments';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::CREATE_PROJECTS,
            self::EDIT_PROJECTS,
            self::CREATE_TASKS,
            self::ASSIGN_TASKS,
            self::EDIT_TASK_FIELDS,
            self::DELETE_TASKS,
            self::ADVANCE_TASK_STATUS,
            self::QA_DECIDE,
            self::MANAGE_RESOURCES,
            self::ADD_COMMENTS,
        ];
    }
}
