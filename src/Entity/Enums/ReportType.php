<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum ReportType: string
{
    use Enums;

    case Null = '';
    case Camera = '1';
    case Modem = '2';
    case Msam = '3';
    case Server = '4';
    case Switch = '5';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Camera => 'Camara',
            self::Modem => 'Modem',
            self::Msam => 'Msam',
            self::Server => 'Servidor',
            self::Switch => 'Switch',
            default => '-Seleccione-'
        };
    }
}
