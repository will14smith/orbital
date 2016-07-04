<?php

namespace AppBundle\Services\Enum;

class Unit
{
    const METER = 'm';
    const CENTIMETER = 'cm';
    const YARD = 'yd';

    public static $choices = [
        self::METER => 'Meter',
        self::CENTIMETER => 'Centimeter',
        self::YARD => 'Yard',
    ];

    // $src_value * $matrix[$src][$dst] = $dst_value
    private static $matrix = [
        self::METER => [
            self::CENTIMETER => 100,
            self::YARD => 1.0936133,
        ],
        self::CENTIMETER => [
            self::METER => 0.01,
            self::YARD => 0.010936133,
        ],
        self::YARD => [
            self::METER => 0.9144,
            self::CENTIMETER => 91.44,
        ],
    ];

    public static function convert($value, $src, $dst)
    {
        if ($src == $dst) {
            return $value;
        }

        return $value * self::$matrix[$src][$dst];
    }
}
