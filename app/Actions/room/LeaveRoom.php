<?php

declare(strict_types=1);

namespace App\Actions\room;

use App\Events\UserLeftRoomEvent;
use App\Models\Room;

final class LeaveRoom
{
    public function __invoke(Room $room, int $userId): void
    {
        $room->users()->detach($userId);

        UserLeftRoomEvent::dispatch($room, $userId);
    }
}
