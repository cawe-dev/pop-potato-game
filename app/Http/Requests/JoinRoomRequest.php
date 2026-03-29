<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Room\RoomType;
use App\Services\Room\IRoomService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class JoinRoomRequest extends FormRequest
{
    public function __construct(protected IRoomService $service) {}

    public function authorize(): bool
    {
        $roomCode = $this->route('code');
        $room = $this->service->showByCode($roomCode);

        return $this->user()->can('join', $room);
    }

    public function rules(): array
    {
        return [
            'max_users' => 'prohibited',
            'code'      => 'prohibited',
            'password'  => 'required_if:type,private|min:4|string|nullable|prohibited_if:type,public',
            'theme'     => 'prohibited',
            'icon'      => 'prohibited',
            'type'      => [Rule::enum(RoomType::class), 'required'],
            'game_mode' => 'prohibited',
        ];
    }
}
