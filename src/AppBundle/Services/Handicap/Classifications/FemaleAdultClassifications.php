<?php

namespace AppBundle\Services\Handicap\Classifications;

use AppBundle\Entity\Round;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Gender;

class FemaleAdultClassifications extends BaseClassifications
{
    private static $table;

    private static function getTable()
    {
        if (!self::$table) {
            self::$table = [
                BowType::RECURVE => self::buildHandicapRow(65, 57, 50, 41, 33, 27),
                BowType::LONGBOW => self::buildHandicapRow(82, 73, 70, 65, 62, 59),
                BowType::COMPOUND => self::buildHandicapRow(56, 49, 38, 29, 21, 15),
                BowType::BAREBOW => self::buildHandicapRow(78, 71, 64, 57, 51, 49),
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
        return Gender::FEMALE;
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
        if ($max >= 70 && $arrows >= 144) {
            return Classification::GRAND_MASTER_BOWMAN;
        }

        // max >= 70m for BM
        if ($max >= 70) {
            return Classification::BOWMAN;
        }

        // max >= 70m for 1st
        if ($max >= 60) {
            return Classification::FIRST;
        }

        // max >= 50m for 2nd
        if ($max >= 50) {
            return Classification::SECOND;
        }

        // max >= 40m for 3rd
        if ($max >= 40) {
            return Classification::THIRD;
        }

        return null;
    }
}
