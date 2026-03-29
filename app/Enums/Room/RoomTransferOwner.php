<?php

declare(strict_types=1);

namespace App\Enums\Room;

enum RoomTransferOwner: string
{
    case AUTO = 'auto';
    case MANUAL = 'manual';
}
