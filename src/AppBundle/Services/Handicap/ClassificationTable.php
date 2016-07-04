<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Gender;

// TODO handle juniors

class ClassificationTable
{
    public static $handicapTable;

    public static function initHandicapTable()
    {
        $table = [];

        $table[Gender::MALE] = [];

        $table[Gender::MALE][BowType::RECURVE] = self::buildHandicapRow(58, 50, 44, 36, 28, 22);
        $table[Gender::MALE][BowType::LONGBOW] = self::buildHandicapRow(74, 69, 65, 60, 55, 52);
        $table[Gender::MALE][BowType::COMPOUND] = self::buildHandicapRow(48, 38, 32, 23, 16, 10);
        $table[Gender::MALE][BowType::BAREBOW] = self::buildHandicapRow(71, 64, 56, 49, 45, 40);

        $table[Gender::FEMALE] = [];

        $table[Gender::FEMALE][BowType::RECURVE] = self::buildHandicapRow(65, 57, 50, 41, 33, 27);
        $table[Gender::FEMALE][BowType::LONGBOW] = self::buildHandicapRow(82, 73, 70, 65, 62, 59);
        $table[Gender::FEMALE][BowType::COMPOUND] = self::buildHandicapRow(56, 49, 38, 29, 21, 15);
        $table[Gender::FEMALE][BowType::BAREBOW] = self::buildHandicapRow(78, 71, 64, 57, 51, 49);

        self::$handicapTable = $table;
    }

    /** @noinspection PhpTooManyParametersInspection */
    public static function buildHandicapRow($third, $second, $first, $bowman, $master, $grand)
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

    public static function getHandicap($gender, $bowtype, $classification)
    {
        return self::$handicapTable[$gender][$bowtype][$classification];
    }
}

ClassificationTable::initHandicapTable();
