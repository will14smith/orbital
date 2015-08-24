<?php

namespace AppBundle\Utilities;

class DateUtils
{
    static function getRoundedNow()
    {
        return self::round(new \DateTime());
    }

    static function round(\DateTime $dateTime)
    {
        $hours = (int)$dateTime->format('H');
        $mins = (int)$dateTime->format('i');

        $dateTime->setTime($hours, $mins - ($mins % 15) + 15);

        return $dateTime;
    }
}