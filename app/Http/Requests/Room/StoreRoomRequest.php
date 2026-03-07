<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\RoomGameMode;
use App\RoomTheme;
use App\RoomType;
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
            'password'  => 'min:4|string|nullable',
            'theme'     => [Rule::enum(RoomTheme::class), 'required'],
            'icon'      => 'required|string',
            'type'      => [Rule::enum(RoomType::class), 'required'],
            'game_mode' => [Rule::enum(RoomGameMode::class), 'required'],
        ];
    }
}
