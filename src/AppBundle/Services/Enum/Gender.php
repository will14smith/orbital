<?php


namespace AppBundle\Services\Enum;


class Gender {
    const MALE = 'male';
    const FEMALE = 'female';

    static $choices = [Gender::MALE, Gender::FEMALE];
}