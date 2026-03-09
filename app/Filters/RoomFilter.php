<?php

declare(strict_types=1);

namespace App\Filters;

final class RoomFilter extends QueryFilter
{
    public function code(string $code): void
    {
        $this->builder->where('code', $code);
    }

    public function type(string $type): void
    {
        $this->builder->where('type', $type);
    }

    public function game_mode(array $modes): void
    {
        $this->builder->whereIn('game_mode', $modes);
    }

    public function theme(array $themes): void
    {
        $this->builder->whereIn('theme', $themes);
    }
}
