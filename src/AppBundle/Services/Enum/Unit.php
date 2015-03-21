<?php

namespace AppBundle\Services\Enum;

class Unit {
    const METER = 'm';
    const CENTIMETER = 'cm';
    const YARD = 'yd';

    public static $choices = [
        Unit::METER => 'Meter',
        Unit::CENTIMETER => 'Centimeter',
        Unit::YARD => 'Yard'
    ];
}