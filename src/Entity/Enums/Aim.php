<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum Aim: string
{
    use EnumsTrait;

    case Null = '';
    case Objective = '1';
    case NoObjective = '0';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Objective => 'Objetivo',
            self::NoObjective => 'Vía',
            default => '-Seleccione-'
        };
    }
}
