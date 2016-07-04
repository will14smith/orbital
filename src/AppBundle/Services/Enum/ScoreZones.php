<?php

namespace AppBundle\Services\Enum;

class ScoreZones
{
    const METRIC = 'metric';
    const IMPERIAL = 'imperial';
    const TRIPLE = 'triple';
    const WORCESTER = 'worcester';
    const FITA6 = 'fita6';

    public static $choices = [
        self::METRIC => '10-zone metric (X, 10, 9, ...)',
        self::IMPERIAL => '5-zone imperial (9, 7, 5, ...)',
        self::TRIPLE => '5-zone metric (X, 10, ..., 6)',
        self::WORCESTER => 'Worcester',
        self::FITA6 => 'FITA6',
    ];
}
