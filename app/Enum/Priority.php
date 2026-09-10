<?php

namespace App\Enum;

enum Priority: string
{
    case RENDAH = 'rendah';
    case SEDANG = 'sedang';
    case TINGGI = 'tinggi';

    public function label(): string
    {
        return match ($this) {
            self::RENDAH => 'Rendah',
            self::SEDANG => 'Sedang',
            self::TINGGI => 'Tinggi',
        };
    }
}
