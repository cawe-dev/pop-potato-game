<?php

declare(strict_types=1);

namespace App;

enum RoomType: string
{
    case PRIVATE = 'private';
    case PUBLIC = 'public';

    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => 'Private',
            self::PUBLIC  => 'Public',
        };
    }
}
