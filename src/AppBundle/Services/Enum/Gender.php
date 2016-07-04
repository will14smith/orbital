<?php

namespace AppBundle\Services\Enum;

class Gender
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static $choices = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    public static function display($gender)
    {
        return self::$choices[$gender];
    }
}
