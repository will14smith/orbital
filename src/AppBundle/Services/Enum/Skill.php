<?php

namespace AppBundle\Services\Enum;

class Skill
{
    const NOVICE = 'novice';
    const SENIOR = 'senior';

    public static $choices = [
        self::SENIOR => 'Senior',
        self::NOVICE => 'Novice',
    ];

    public static function display($skill)
    {
        return self::$choices[$skill];
    }

    public static function normaliseStartDate(\DateTime $date)
    {
        $normal = clone $date;
        $normal->setDate($date->format('Y'), 9, 1);
        $normal->setTime(0, 0, 0);
        if ($normal > $date) {
            $normal->sub(new \DateInterval('P1Y'));
        }

        return $normal;
    }

    public static function getSkillOn(\DateTime $date_started, \DateTime $date_ref)
    {
        $date_started_norm = self::normaliseStartDate($date_started);
        $date_ref_norm = self::normaliseStartDate($date_ref);

        if (self::date_equal($date_started_norm, $date_ref_norm)) {
            return self::NOVICE;
        }

        return self::SENIOR;
    }

    private static function date_equal(\DateTime $a, \DateTime $b)
    {
        return $a->format('Y-m-d') === $b->format('Y-m-d');
    }
}
