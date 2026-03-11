<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Room\RoomType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class JoinRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
