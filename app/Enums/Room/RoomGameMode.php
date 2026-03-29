<?php

declare(strict_types=1);

namespace App\Enums\Room;

enum RoomGameMode: string
{
    case DEFAULT = 'default';
    case HARD_POTATO = 'hard_potato';

    public function label(): string
    {
        return match ($this) {
            self::DEFAULT     => 'Default',
            self::HARD_POTATO => 'Hard Potato',
        };
    }
}
