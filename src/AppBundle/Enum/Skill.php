<?php


namespace AppBundle\Enum;


class Skill {
    const NOVICE = 'novice';
    const SENIOR = 'senior';

    static $choices = [Skill::NOVICE, Skill::SENIOR];
}