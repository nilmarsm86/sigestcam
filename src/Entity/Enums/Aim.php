<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum Aim: int
{
    use Enums;

    case Objective = 1;
    case NoObjective = 0;

    public static function getLabelFrom(BackedEnum|int $enum): string
    {
        if(is_int($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Objective => 'Objetivo',
            self::NoObjective => 'No objetivo',
        };
    }
}
