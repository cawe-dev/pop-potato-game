<?php

declare(strict_types=1);

namespace App\Repository\Eloquent\Room;

use App\Models\Room;
use App\Repository\Eloquent\BaseRepository;

final class RoomRepository extends BaseRepository implements IRoomRepository
{
    public function __construct(Room $model)
    {
        parent::__construct($model);
    }
}
