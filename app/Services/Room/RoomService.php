<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Actions\room\JoinRoom;
use App\Actions\room\LeaveRoom;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomTransferOwner;
use App\Enums\Room\RoomType;
use App\Models\Room;
use App\Repository\Eloquent\Room\IRoomRepository;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class RoomService extends BaseService implements IRoomService
{
    public function __construct(IRoomRepository $repository, private JoinRoom $join, private LeaveRoom $leave)
    {
        parent::__construct($repository);
    }

    public function showByCode(string $code): Room
    {
        return $this->repository->findByCode($code);
    }

    public function join(string $code, ?string $password, int $userId): Room
    {
        $room = $this->repository->findByCode($code);

        throw_if(
            !empty($password) && $room->password !== $password,
            Exception::class,
            'invalid password'
        );

        ($this->join)($room, $userId);

        return $room;
    }

    public function leave(string $code, int $userId): void
    {
        $room = $this->repository->findByCode($code);
        ($this->leave)($room, $userId);
    }

    public function nextOwner(Room $room, RoomTransferOwner $rule = RoomTransferOwner::AUTO, ?int $userId = null): Room
    {
        $nextOwnerId = match ($rule) {
            RoomTransferOwner::MANUAL => $userId ?? throw new InvalidArgumentException('Require user id'),
            RoomTransferOwner::AUTO   => $this->repository->findFirstMember($room->id)?->id,
        };

        if (!$nextOwnerId) {
            return $room;
        }

        return $this->repository->newOwner($room->id, $nextOwnerId);
    }

    public function nextOwnerByCode(string $code, RoomTransferOwner $rule = RoomTransferOwner::AUTO, ?int $userId = null): Room
    {
        $room = $this->showByCode($code);

        return $this->nextOwner(
            $room,
            $rule,
            $userId
        );
    }

    protected function beforeStore(array $data): array
    {
        $data = $data
        |> $this->clearPassword(...)
        |> $this->alterIcon(...);

        return [
            ...$data,
            'code' => $this->generateRoomCode(),
        ];
    }

    protected function afterStore(Model $room, array $data): void
    {
        ($this->join)($room, $data['user_id']);
    }

    protected function beforeUpdate(array $data): array
    {
        return $data
        |> $this->clearPassword(...)
        |> $this->alterIcon(...);
    }

    private function generateRoomCode(): string
    {
        return Str::random(4)
        |> Str::upper(...);
    }

    private function clearPassword(array $data): array
    {
        if (isset($data['type']) && $data['type'] !== RoomType::PRIVATE->value) {
            $data['password'] = null;
        }

        return $data;
    }

    private function alterIcon(array $data): array
    {
        if (isset($data['theme'])) {
            $data['icon'] = RoomTheme::from($data['theme'])->icon();
        }

        return $data;
    }
}
