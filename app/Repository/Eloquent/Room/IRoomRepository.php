<?php

declare(strict_types=1);

namespace App\Repository\Eloquent\Room;

use App\Models\Room;
use App\Models\User;

interface IRoomRepository
{
    public function findByCode(string $code): Room;

    public function newOwner(int $id, int $newOwnerId): Room;

    public function findFirstMember(int $id): ?User;
}
