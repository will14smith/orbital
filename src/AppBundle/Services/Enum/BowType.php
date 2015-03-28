<?php


namespace AppBundle\Services\Enum;


class BowType
{
    const RECURVE = 'recurve';
    const BAREBOW = 'barebow';
    const LONGBOW = 'longbow';
    const TRADITIONAL = 'traditional';
    const COMPOUND = 'compound';

    static $choices = [
        BowType::RECURVE => 'Recurve',
        BowType::BAREBOW => 'Barebow',
        BowType::LONGBOW => 'Longbow',
        BowType::TRADITIONAL => 'Traditional',
        BowType::COMPOUND => 'Compound'
    ];

    public static function display($bow_type)
    {
        return BowType::$choices[$bow_type];
    }
}