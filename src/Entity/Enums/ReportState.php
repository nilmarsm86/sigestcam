<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum ReportState: string
{
    use Enums;

    case Null = '';
    case Open = '1';
    case Close = '0';

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
