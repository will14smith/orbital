<?php


namespace AppBundle\Enum;


class Gender {
    const MALE = 'male';
    const FEMALE = 'female';

    static $choices = [Gender::MALE, Gender::FEMALE];
}