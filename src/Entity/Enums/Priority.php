<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum Priority: string
{
    use EnumsTrait;

    case Null = '';
    case Hight = '1';
    case Medium = '0';
    case Low = '-1';

    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Hight => 'Alta',
            self::Medium => 'Media',
            self::Low => 'Baja',
            default => '-Seleccione-'
        };
    }
}
