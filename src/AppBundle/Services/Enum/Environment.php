<?php

namespace AppBundle\Services\Enum;


class Environment
{
    const INDOOR = true;
    const OUTDOOR = false;

    static $choices = [
        Environment::INDOOR => 'Indoor',
        Environment::OUTDOOR => 'Outdoor',
    ];

    public static function display($env)
    {
        return Environment::$choices[$env];
    }
}