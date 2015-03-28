<?php


namespace AppBundle\Services\Enum;


class Gender
{
    const MALE = 'male';
    const FEMALE = 'female';

    static $choices = [
        Gender::MALE => 'Male',
        Gender::FEMALE => 'Female'
    ];

    public static function display($gender)
    {
        return Gender::$choices[$gender];
    }
}