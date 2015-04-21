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
        BadgeCategory::COLOUR => 'Colour',
        BadgeCategory::SKILL => 'Skill',
        BadgeCategory::EQUIPMENT => 'Equipment',
        BadgeCategory::SOCIAL => 'Social',
        BadgeCategory::COMMITTEE => 'Committee',
        BadgeCategory::BESPOKE => 'Bespoke',
        BadgeCategory::OTHER => 'Other',
    ];
}
