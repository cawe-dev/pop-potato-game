<?php

declare(strict_types=1);

namespace App\Repository\Eloquent\Room;

use App\Models\Room;
use App\Models\User;
use App\Repository\Eloquent\BaseRepository;

final class RoomRepository extends BaseRepository implements IRoomRepository
{
    public function __construct(Room $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): Room
    {
        return $this->model->where('code', $code)->firstOrFail();
    }

    public function newOwner(int $id, int $newOwnerId): Room
    {
        return $this->update($id, ['user_id' => $newOwnerId]);
    }

    public function findFirstMember(int $id): ?User
    {
        return $this->model->find($id)?->users()->first();
    }
}
