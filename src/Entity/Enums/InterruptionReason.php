<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum InterruptionReason: string
{
    use EnumsTrait;

    case Rip = '1';
    case Connectivity = '2';
    case ElectricFluid = '3';
    case Substitution = '4';
    case Modem = '5';
    case WithoutLink = '6';
    case Null = '-1';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Rip => 'Rotura',
            self::Connectivity => 'Conectividad',
            self::ElectricFluid => 'Fluido eléctrico',
            self::Substitution => 'Sustitución',
            self::Modem => 'Modem',
            self::WithoutLink => 'Sin enlace',
            self::Null => 'Otra',
            default => '-Seleccione-'
        };
    }
}
