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
        Classification::THIRD => 'Third Class',
    ];

    public static function display($classification)
    {
        return Classification::$choices[$classification];
    }
}