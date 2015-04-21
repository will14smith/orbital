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
        ScoreZones::METRIC => '10-zone metric (X, 10, 9, ...)',
        ScoreZones::IMPERIAL => '5-zone imperial (9, 7, 5, ...)',
        ScoreZones::TRIPLE => '5-zone metric (X, 10, ..., 6)',
        ScoreZones::WORCESTER => 'Worcester',
        ScoreZones::FITA6 => 'FITA6',
    ];
}
