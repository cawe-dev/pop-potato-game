<?php

declare(strict_types=1);

namespace App\Services\Room;

interface IRoomService
{
    public function generateRoomCode(): string;
}
