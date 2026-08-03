<?php

namespace App\Support;

class Role
{
    const ADMIN = 'admin';

    const PM = 'pm';

    const DEVELOPER = 'developer';

    const QA = 'qa';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [self::ADMIN, self::PM, self::DEVELOPER, self::QA];
    }
}
