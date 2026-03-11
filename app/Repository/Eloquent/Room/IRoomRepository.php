<?php

declare(strict_types=1);

namespace App\Repository\Eloquent\Room;

use App\Models\Room;

interface IRoomRepository
{
    public function findByCode(string $code): Room;
}
