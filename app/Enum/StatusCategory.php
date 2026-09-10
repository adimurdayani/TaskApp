<?php

namespace App\Enum;

enum StatusCategory: string
{
    case AKTIF = 'aktif';
    case TIDAK_AKTIF = 'tidak_aktif';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::TIDAK_AKTIF => 'Tidak Aktif',
        };
    }
    public function isActive(): bool
    {
        return match ($this) {
            self::AKTIF => true,
            self::TIDAK_AKTIF => false,
        };
    }
}
