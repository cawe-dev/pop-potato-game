<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Models\Room;

interface IRoomService
{
    public function join(string $code, ?string $password, int $userId): Room;
}
