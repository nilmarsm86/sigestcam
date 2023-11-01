<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum Flaw: string
{
    use EnumsTrait;

    case WithoutLink = '1';
    case Null = '-1';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::WithoutLink => 'Sin enlace',
            self::Null => 'Otra',
            default => '-Seleccione-'
        };
    }
}
