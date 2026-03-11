<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomStatus;
use App\Enums\Room\RoomTheme;
use App\Enums\Room\RoomType;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    protected $hidden = [
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, QueryFilter $filters): Builder
    {
        return $filters->apply($query);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
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
