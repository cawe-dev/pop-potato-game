<?php

declare(strict_types=1);

namespace App\Policies;

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

    public function update(?User $user, Room $room): bool
    {
        return $user->id === $room->user_id;
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
