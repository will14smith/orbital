<?php

namespace AppBundle\Services\Enum;

class Environment
{
    const INDOOR = true;
    const OUTDOOR = false;

    public static $choices = [
        self::INDOOR => 'Indoor',
        self::OUTDOOR => 'Outdoor',
    ];

    public static function display($env)
    {
        return self::$choices[$env];
    }
}
