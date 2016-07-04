<?php

namespace AppBundle\Services\Enum;

class BowType
{
    const RECURVE = 'recurve';
    const BAREBOW = 'barebow';
    const LONGBOW = 'longbow';
    const TRADITIONAL = 'traditional';
    const COMPOUND = 'compound';

    public static $choices = [
        self::RECURVE => 'Recurve',
        self::BAREBOW => 'Barebow',
        self::LONGBOW => 'Longbow',
        self::TRADITIONAL => 'Traditional',
        self::COMPOUND => 'Compound',
    ];

    public static function display($bow_type)
    {
        return self::$choices[$bow_type];
    }
}
