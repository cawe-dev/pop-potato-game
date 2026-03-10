<?php

declare(strict_types=1);

namespace App\Services\Room;

use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
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
        $password = $data['password'];

        if ($data['type'] !== RoomType::PRIVATE->value) {
            $password = null;
        }

        return array_merge($data, [
            'code'     => $this->generateRoomCode(),
            'icon'     => RoomTheme::from($data['theme'])->icon(),
            'password' => $password,
        ]);
    }

    private function generateRoomCode(): string
    {
        return Str::random(4)
        |> Str::upper(...);
    }
}
