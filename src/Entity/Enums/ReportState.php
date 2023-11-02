<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum ReportState: string
{
    use EnumsTrait;

    case Null = '';
    case Open = '1';
    case Close = '0';

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
            self::Open => 'Abierto',
            self::Close => 'Cerrado',
            default => '-Seleccione-'
        };
    }
}
