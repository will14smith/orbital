<?php


namespace AppBundle\Services\Enum;


class BowType {
    const RECURVE = 'recurve';
    const BAREBOW = 'barebow';
    const LONGBOW = 'longbow';
    const TRADITIONAL = 'traditional';
    const COMPOUND = 'compound';

    static $choices = [BowType::RECURVE, BowType::BAREBOW,
        BowType::LONGBOW, BowType::TRADITIONAL, BowType::COMPOUND];
}