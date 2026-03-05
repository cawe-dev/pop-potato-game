<?php

declare(strict_types=1);

namespace App;

enum RoomStatus: string
{
    case WAITING = 'waiting';
    case PLAYING = 'playing';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::WAITING  => 'Waiting',
            self::PLAYING  => 'Playing',
            self::FINISHED => 'Finished',
        };
    }
}
