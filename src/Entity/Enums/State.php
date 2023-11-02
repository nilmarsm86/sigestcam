<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum State: string
{
    use EnumsTrait;

    case Null = '';
    case Active = '1';
    case Inactive = '0';

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
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            default => '-Seleccione-'
        };
    }
}
