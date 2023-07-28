<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;
use BackedEnum;

enum State: string
{
    use Enums;

    case Null = '';
    case Active = '1';
    case Inactive = '0';

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
