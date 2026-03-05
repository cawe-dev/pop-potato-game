<?php

declare(strict_types=1);

namespace App;

enum RoomTheme: string
{
    case FILMS = 'films';
    case SPACE = 'space';
    case FANTASY_BOOKS = 'fantasy_books';
    case UNDERWATER = 'underwater';
    case PROGRAMMING = 'programming';

    public function label(): string
    {
        return match ($this) {
            self::FANTASY_BOOKS => 'Fantasy Books',
            self::FILMS         => 'Films',
            self::SPACE         => 'Space',
            self::UNDERWATER    => 'Underwater',
            self::PROGRAMMING   => 'Programming',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FANTASY_BOOKS => 'lucide:book-open',
            self::FILMS         => 'lucide:film',
            self::SPACE         => 'lucide:rocket',
            self::UNDERWATER    => 'lucide:fish',
            self::PROGRAMMING   => 'lucide:code',
        };
    }
}
