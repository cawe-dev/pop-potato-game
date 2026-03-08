<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomStatus;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'icon',
        'password',
        'theme',
        'type',
        'max_users',
        'game_mode',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'type'      => RoomType::class,
            'status'    => RoomStatus::class,
            'theme'     => RoomTheme::class,
            'game_mode' => RoomGameMode::class,
        ];
    }
}
