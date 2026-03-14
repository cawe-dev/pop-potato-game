<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\Services\Room\IRoomService;
use Illuminate\Foundation\Http\FormRequest;

final class KickRoomRequest extends FormRequest
{
    public function __construct(protected IRoomService $service) {}

    public function authorize(): bool
    {
        $roomCode = $this->route('code');
        $room = $this->service->showByCode($roomCode);

        return $this->user()->can('kick', $room);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
