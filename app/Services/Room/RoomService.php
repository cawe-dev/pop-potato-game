<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Actions\room\JoinRoom;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use App\Repository\Eloquent\Room\IRoomRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class RoomService extends BaseService implements IRoomService
{
    public function __construct(IRoomRepository $repository, private JoinRoom $join)
    {
        parent::__construct($repository);
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

    protected function afterStore(Model $model, array $data): void
    {
        ($this->join)($model, $data['user_id']);
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
