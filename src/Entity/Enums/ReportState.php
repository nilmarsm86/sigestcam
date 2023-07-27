<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum ReportState: int
{
    use Enums;

    case Open = 1;
    case Close = 0;

    public static function getLabelFrom(BackedEnum|int $enum): string
    {
        if(is_int($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Open => 'Abierto',
            self::Close => 'Cerrado',
        };
    }
}
