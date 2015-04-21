<?php

namespace AppBundle\Services\Enum;

class Unit
{
    const METER = 'm';
    const CENTIMETER = 'cm';
    const YARD = 'yd';

    public static $choices = [
        Unit::METER => 'Meter',
        Unit::CENTIMETER => 'Centimeter',
        Unit::YARD => 'Yard'
    ];

    // $src_value * $matrix[$src][$dst] = $dst_value
    private static $matrix = [
        Unit::METER => [
            Unit::CENTIMETER => 100,
            Unit::YARD => 1.0936133,
        ],
        Unit::CENTIMETER => [
            Unit::METER => 0.01,
            Unit::YARD => 0.010936133,
        ],
        Unit::YARD => [
            Unit::METER => 0.9144,
            Unit::CENTIMETER => 91.44,
        ]
    ];

    public static function convert($value, $src, $dst)
    {
        if ($src == $dst) {
            return $value;
        }

        return $value * self::$matrix[$src][$dst];
    }
}
