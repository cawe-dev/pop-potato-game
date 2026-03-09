<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Repository\Eloquent\Room\IRoomRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

final class RoomService extends BaseService implements IRoomService
{
    public function __construct(IRoomRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeStore(array $data): array
    {
        return array_merge($data, [
            'code' => $this->generateRoomCode(),
        ]);
    }

    private function generateRoomCode(): string
    {
        return Str::random(4)
        |> Str::upper(...);
    }
}
