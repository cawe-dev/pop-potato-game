<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use App\Services\Room\IRoomService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRoomRequest extends FormRequest
{
    public function __construct(protected IRoomService $service) {}

    public function authorize(): bool
    {
        $roomId = (int) $this->route('room');
        $room = $this->service->show($roomId);

        return $this->user()->can('update', $room);
    }

    public function rules(): array
    {
        return [
            'max_users' => 'numeric|min:2',
            'code'      => 'prohibited',
            'password'  => 'required_if:type,private|min:4|string|nullable|prohibited_if:type,public',
            'theme'     => [Rule::enum(RoomTheme::class)],
            'icon'      => 'prohibited',
            'type'      => [Rule::enum(RoomType::class)],
            'game_mode' => [Rule::enum(RoomGameMode::class)],
        ];
    }
}
