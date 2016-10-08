<?php

namespace AppBundle\Services\Handicap\Classifications;

use AppBundle\Entity\Round;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Gender;

class MaleAdultClassifications extends BaseClassifications
{
    private static $table;

    private static function getTable()
    {
        if (!self::$table) {
            self::$table = [
                BowType::RECURVE => self::buildHandicapRow(58, 50, 44, 36, 28, 22),
                BowType::LONGBOW => self::buildHandicapRow(74, 69, 65, 60, 55, 52),
                BowType::COMPOUND => self::buildHandicapRow(48, 38, 32, 23, 16, 10),
                BowType::BAREBOW => self::buildHandicapRow(71, 64, 56, 49, 45, 40),
            ];
        }

        return self::$table;
    }

    /**
     * @return int|null
     */
    public function getMaximumAge()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return Gender::MALE;
    }

    /**
     * @param string $bowtype
     * @param string $classification
     *
     * @return int
     */
    public function getHandicap(string $bowtype, string $classification)
    {
        return self::getTable()[$bowtype][$classification];
    }

    /**
     * @param Round $round
     *
     * @return string|null;
     */
    public function getMaxClassification(Round $round)
    {
        $max = $this->getMaxDistance($round);
        $arrows = $this->getArrowCount($round);

        // max >= 90m + 144 arrows for MB & GMB
        if ($max >= 90 && $arrows >= 144) {
            return Classification::GRAND_MASTER_BOWMAN;
        }

        // max >= 90m for BM
        if ($max >= 90) {
            return Classification::BOWMAN;
        }

        // max >= 70m for 1st
        if ($max >= 70) {
            return Classification::FIRST;
        }

        // max >= 60m for 2nd
        if ($max >= 60) {
            return Classification::SECOND;
        }

        // max >= 50m for 3rd
        if ($max >= 50) {
            return Classification::THIRD;
        }

        return null;
    }
}
