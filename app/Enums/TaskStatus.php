<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Qa = 'qa';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In progress',
            self::Qa => 'QA',
            self::Done => 'Done',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::Pending => 'dot-pending',
            self::InProgress => 'dot-progress',
            self::Qa => 'dot-qa',
            self::Done => 'dot-done',
        };
    }
}
