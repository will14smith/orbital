<?php

namespace AppBundle\Services\Enum;

class Classification
{
    const THIRD = '3rd';
    const SECOND = '2nd';
    const FIRST = '1st';
    const BOWMAN = 'bm';
    const MASTER_BOWMAN = 'mb';
    const GRAND_MASTER_BOWMAN = 'gmb';

    public static $choices = [
        self::THIRD => 'Third Class',
        self::SECOND => 'Second Class',
        self::FIRST => 'First Class',
        self::BOWMAN => 'Bowman',
        self::MASTER_BOWMAN => 'Master Bowman',
        self::GRAND_MASTER_BOWMAN => 'Grand Master Bowman',
    ];

    public static $shortChoices = [
        self::THIRD => '3rd',
        self::SECOND => '2nd',
        self::FIRST => '1st',
        self::BOWMAN => 'BM',
        self::MASTER_BOWMAN => 'MB',
        self::GRAND_MASTER_BOWMAN => 'GMB',
    ];

    public static function display($classification)
    {
        return self::$choices[$classification];
    }
}
