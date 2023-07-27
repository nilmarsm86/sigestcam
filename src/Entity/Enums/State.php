<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum State: int
{
    use Enums;

    case Active = 1;
    case Inactive = 0;

    public static function getLabelFrom(BackedEnum|int $enum): string
    {
        if(is_int($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
        };
    }




}
