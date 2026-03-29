<?php

declare(strict_types=1);

namespace App\Actions\room;

use App\Models\Room;

final class JoinRoom
{
    public function __invoke(Room $room, int $userId): void
    {
        $room->users()->syncWithoutDetaching([$userId]);
    }
}
