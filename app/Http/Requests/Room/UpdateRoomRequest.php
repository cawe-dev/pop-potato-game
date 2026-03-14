<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomStatus;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use App\Models\Room;
use App\Services\Room\IRoomService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UpdateRoomRequest extends FormRequest
{
    private ?Room $roomInstance = null;

    public function __construct(protected IRoomService $service) {}

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->getRoom());
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
            'status'    => [Rule::enum(RoomStatus::class)],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->input('status') !== RoomStatus::PLAYING->value) {
                    return;
                }

                $room = $this->getRoom();

                if ($room->users->count() < 2) {
                    $validator->errors()->add(
                        'status',
                        'The match cannot start with fewer than 2 players.'
                    );
                }
            },
        ];
    }

    private function getRoom(): Room
    {
        return $this->roomInstance ??= $this->service->show((int) $this->route('room'));
    }
}
