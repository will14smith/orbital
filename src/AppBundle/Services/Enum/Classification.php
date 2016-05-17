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

    static $choices = [
        Classification::THIRD => 'Third Class',
        Classification::SECOND => 'Second Class',
        Classification::FIRST => 'First Class',
        Classification::BOWMAN => 'Bowman',
        Classification::MASTER_BOWMAN => 'Master Bowman',
        Classification::GRAND_MASTER_BOWMAN => 'Grand Master Bowman',
    ];

    static $shortChoices = [
        Classification::THIRD => '3rd',
        Classification::SECOND => '2nd',
        Classification::FIRST => '1st',
        Classification::BOWMAN => 'BM',
        Classification::MASTER_BOWMAN => 'MB',
        Classification::GRAND_MASTER_BOWMAN => 'GMB',
    ];

    public static function display($classification)
    {
        return Classification::$choices[$classification];
    }
}