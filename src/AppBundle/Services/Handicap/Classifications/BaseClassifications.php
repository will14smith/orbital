<?php

namespace AppBundle\Services\Handicap\Classifications;

use AppBundle\Entity\Round;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Unit;

abstract class BaseClassifications
{
    /**
     * @return int|null
     */
    public abstract function getMaximumAge();

    /**
     * @return string
     */
    public abstract function getGender();

    /**
     * @param string $bowtype
     * @param string $classification
     *
     * @return int
     */
    public abstract function getHandicap(string $bowtype, string $classification);

    /**
     * @param Round $round
     *
     * @return string|null
     */
    public abstract function getMaxClassification(Round $round);

    /** @noinspection PhpTooManyParametersInspection
     * @param int $third
     * @param int $second
     * @param int $first
     * @param int $bowman
     * @param int $master
     * @param int $grand
     *
     * @return array
     */
    protected function buildHandicapRow(int $third, int $second, int $first, int $bowman, int $master, int $grand)
    {
        return [
            Classification::THIRD => $third,
            Classification::SECOND => $second,
            Classification::FIRST => $first,
            Classification::BOWMAN => $bowman,
            Classification::MASTER_BOWMAN => $master,
            Classification::GRAND_MASTER_BOWMAN => $grand,
        ];
    }

    /**
     * @param Round $round
     *
     * @return float
     */
    protected function getMaxDistance(Round $round): float
    {
        $max = 0;

        foreach ($round->getTargets() as $target) {
            $dist = Unit::convert($target->getDistanceValue(), $target->getDistanceUnit(), Unit::METER);

            if ($dist > $max) {
                $max = $dist;
            }
        }

        return $max;
    }

    /**
     * @param Round $round
     *
     * @return int
     */
    protected function getArrowCount(Round $round): int
    {
        return $round->getTotalArrows();
    }
}
