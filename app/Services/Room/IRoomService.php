<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Enums\Room\RoomTransferOwner;
use App\Models\Room;

interface IRoomService
{
    public function showByCode(string $code): Room;

    public function join(string $code, ?string $password, int $userId): Room;

    public function nextOwner(Room $room, RoomTransferOwner $rule = RoomTransferOwner::AUTO, ?int $userId = null): Room;

    public function nextOwnerByCode(string $code, RoomTransferOwner $rule = RoomTransferOwner::AUTO, ?int $userId = null): Room;
}
