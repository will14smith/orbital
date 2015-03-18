<?php

namespace AppBundle\Services\Enum;

class Unit {
    const METER = 'meter';
    const CENTIMETER = 'centimeter';
    const YARD = 'yard';

    public static $choices = [Unit::METER, Unit::CENTIMETER, Unit::YARD];
}