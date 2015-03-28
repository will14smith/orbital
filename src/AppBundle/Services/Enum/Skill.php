<?php


namespace AppBundle\Services\Enum;


class Skill
{
    const NOVICE = 'novice';
    const SENIOR = 'senior';

    static $choices = [
        Skill::NOVICE => 'Novice',
        Skill::SENIOR => 'Senior'
    ];

    public static function display($skill)
    {
        return Skill::$choices[$skill];
    }
}