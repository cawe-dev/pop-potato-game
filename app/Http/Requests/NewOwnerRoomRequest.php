<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Room\IRoomService;
use Illuminate\Foundation\Http\FormRequest;

final class NewOwnerRoomRequest extends FormRequest
{
    public function __construct(protected IRoomService $service) {}

    public function authorize(): bool
    {
        $roomCode = $this->route('code');
        $room = $this->service->showByCode($roomCode);

        return $this->user()->can('update', $room);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
