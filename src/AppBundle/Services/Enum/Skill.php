<?php


namespace AppBundle\Services\Enum;


class Skill {
    const NOVICE = 'novice';
    const SENIOR = 'senior';

    static $choices = [Skill::NOVICE, Skill::SENIOR];
}