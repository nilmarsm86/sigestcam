<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum Priority: int
{
    use Enums;

    case Hight = 1;
    case Medium = 0;
    case Low = -1;

    public static function getLabelFrom(BackedEnum|int $enum): string
    {
        if(is_int($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Hight => 'Alta',
            self::Medium => 'Media',
            self::Low => 'Baja',
        };
    }
}
