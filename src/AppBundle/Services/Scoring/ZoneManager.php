<?php

namespace AppBundle\Services\Scoring;


use AppBundle\Services\Enum\ScoreZones;

class ZoneManager {
    private static $zones = [
        ScoreZones::METRIC => 'AppBundle\Services\Scoring\Zones\MetricCalculator'
    ];

    /**
     * @param string $name
     *
     * @return ZoneCalculator
     * @throws \Exception
     */
    public static function get($name) {
        if(!array_key_exists($name, self::$zones)) {
            throw new \Exception("Unsupported zone");
        }

        $class = static::$zones[$name];

        return new $class;
    }
}
