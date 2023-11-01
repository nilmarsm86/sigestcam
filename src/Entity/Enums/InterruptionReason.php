<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum InterruptionReason: string
{
    use EnumsTrait;

    case Review = '1';
    case Connectivity = '2';
    case ElectricFluid = '3';
    case Camera = '4';
    case Modem = '5';
    //case WithoutLink = '6';
    //case Null = '-1';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Review => 'Pendiente por Revisión',
            self::Connectivity => 'Pendiente por Conectividad',
            self::ElectricFluid => 'Pendiente por Fluido eléctrico',
            self::Camera => 'Pendiente por Cámara',
            self::Modem => 'Pendiente por Modem',
            //self::WithoutLink => 'Sin enlace',
            //self::Null => 'Otra',
            default => '-Seleccione-'
        };
    }
}
