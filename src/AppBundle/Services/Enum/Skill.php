<?php


namespace AppBundle\Services\Enum;


class Skill
{
    const NOVICE = 'novice';
    const SENIOR = 'senior';

    static $choices = [
        Skill::SENIOR => 'Senior',
        Skill::NOVICE => 'Novice'
    ];

    public static function display($skill)
    {
        return Skill::$choices[$skill];
    }
}
