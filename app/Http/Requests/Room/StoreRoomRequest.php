<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'max_users' => 'required|numeric|min:2',
            'code'      => 'required|unique:rooms,code',
            'password'  => 'required_if:type,private|min:4|string|nullable|prohibited_if:type,public',
            'theme'     => [Rule::enum(RoomTheme::class), 'required'],
            'icon'      => 'required|string',
            'type'      => [Rule::enum(RoomType::class), 'required'],
            'game_mode' => [Rule::enum(RoomGameMode::class), 'required'],
        ];
    }
}
