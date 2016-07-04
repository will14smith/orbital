<?php

namespace AppBundle\Utilities;

class DateUtils
{
    public static function getRoundedNow()
    {
        return self::round(new \DateTime());
    }

    public static function round(\DateTime $dateTime)
    {
        $hours = (int) $dateTime->format('H');
        $mins = (int) $dateTime->format('i');

        $dateTime->setTime($hours, $mins - ($mins % 15) + 15);

        return $dateTime;
    }
}
