<?php

namespace App\Entity\Traits;

use BackedEnum;
use Symfony\Component\OptionsResolver\Options;

trait Enums
 {
    /**
     * Get all posible values
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all values for select
     * @return array

    public static function forSelect(): array
    {
        return array_combine(
            array_column(self::cases(), 'name'),//option label
            array_column(self::cases(), 'value')//option value
        );
    }*/

    /**
     * Function fot EnumType
     * @return callable
     */
    public static function getValue(): callable
    {
        return static function (Options $options): ?\Closure {
            return fn (?\BackedEnum $choice): ?string => (null === $choice) ? null : (string) $choice->value;
        };
    }

    /**
     * Label tag for EnumType
     * @return callable
     */
    public static function getLabel(): callable
    {
        return fn (BackedEnum $choice) => self::getLabelFrom($choice);
    }
 }