<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Room\RoomStatus;
use App\Models\Room;
use App\Models\User;

final class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Room $room): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function join(User $user, Room $room): bool
    {
        return $room->max_users > $room->users()->count();
    }

    public function update(User $user, Room $room): bool
    {
        return $user->id === $room->user_id && $room->status === RoomStatus::WAITING;
    }

    public function delete(User $user, Room $room): bool
    {
        return false;
    }

    public function restore(User $user, Room $room): bool
    {
        return false;
    }

    public function forceDelete(User $user, Room $room): bool
    {
        return false;
    }
}
