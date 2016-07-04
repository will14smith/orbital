<?php

namespace AppBundle\Services\Enum;

class BadgeCategory
{
    const COLOUR = 'colour';
    const SKILL = 'skill';
    const EQUIPMENT = 'equipment';
    const SOCIAL = 'social';
    const COMMITTEE = 'committee';
    const BESPOKE = 'bespoke';
    const OTHER = 'other';

    public static $choices = [
        self::COLOUR => 'Colour',
        self::SKILL => 'Skill',
        self::EQUIPMENT => 'Equipment',
        self::SOCIAL => 'Social',
        self::COMMITTEE => 'Committee',
        self::BESPOKE => 'Bespoke',
        self::OTHER => 'Other',
    ];
}
