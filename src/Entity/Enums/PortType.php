<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum PortType: string
{
    use EnumsTrait;

    case Null = '';
    case Electric = '1';
    case Optic = '0';

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
            self::Electric => 'Eléctrico',
            self::Optic => 'Óptico',
            default => '-Seleccione-'
        };
    }

    /**
     * @param BackedEnum|string $enum
     * @return string
     */
    public static function getValueFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Electric => self::Electric->value,
            self::Optic => self::Optic->value,
            default => ''
        };
    }

    /**
     * Get all values for select
     * @return array
     */
    public static function forSelect(): array
    {
        return array_combine(
            ['Null', 'Eléctrico', 'Óptico'],
            array_column(self::cases(), 'value')//option value
        );
    }
}
