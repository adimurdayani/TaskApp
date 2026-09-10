<?php

namespace App\Enum;

enum Status: string
{
    case BELUM_DIMULAI = 'belum_dimulai';
    case DIKERJAKAN = 'dikerjakan';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_DIMULAI => 'Belum Dimulai',
            self::DIKERJAKAN => 'Dikerjakan',
            self::SELESAI => 'Selesai',
        };
    }
}
