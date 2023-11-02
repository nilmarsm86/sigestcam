<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum ConnectionType: string
{
    use EnumsTrait;

    case Null = '';
    case Direct = '1';
    case Simple = '2';
    case SlaveSwitch = '3';
    case SlaveModem = '4';
    case Full = '5';

    /**
     * @param BackedEnum|string $enum
     * @return string
     */
    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Direct => 'Directa',
            self::Simple => 'Simple',
            self::SlaveSwitch => 'Switch esclavo',
            self::SlaveModem => 'Modem esclavo',
            self::Full => 'Completa',
            default => '-Seleccione-'
        };
    }
}
