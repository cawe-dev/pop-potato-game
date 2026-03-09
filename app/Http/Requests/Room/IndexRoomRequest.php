<?php

declare(strict_types=1);

namespace App\Http\Requests\Room;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IndexRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'        => 'nullable|string|min:4|max:4',
            'type'        => [Rule::enum(RoomType::class)],
            'game_mode'   => 'nullable|array',
            'game_mode.*' => [Rule::enum(RoomGameMode::class)],
            'theme'       => 'nullable|array',
            'theme.*'     => [Rule::enum(RoomTheme::class)],
        ];
    }
}
