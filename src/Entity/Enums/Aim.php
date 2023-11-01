<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum Aim: string
{
    use EnumsTrait;

    case Null = '';
    case Objective = '1';
    case Via = '0';
    case Confrontation = '2';
    case Border = '3';
    case Tuition = '4';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Objective => 'Objetivo',
            self::Via => 'Vía',
            self::Confrontation => 'Enfrentamiento',
            self::Border => 'Frontera',
            self::Tuition => 'Matrícula',
            default => '-Seleccione-'
        };
    }
}
