<?php

namespace App\Entity\Traits;

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
     */
    public static function forSelect(): array
    {
        return array_combine(
            array_column(self::cases(), 'name'),//option label
            array_column(self::cases(), 'value')//option value
        );
    }
 }