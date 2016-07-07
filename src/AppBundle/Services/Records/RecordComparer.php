<?php

namespace AppBundle\Services\Records;

use AppBundle\Entity\Record;
use AppBundle\Services\Enum\Unit;

// a > b ==> result > 0
// a => b ==> result = 0
// a < b ==> result < 0

class RecordComparer
{
    /**
     * @param Record $a
     * @param Record $b
     *
     * @return int
     */
    public static function compare(Record $a, Record $b)
    {
        $a_score = self::score($a);
        $b_score = self::score($b);

        if($a_score != $b_score) {
            return $a_score - $b_score;
        }

        throw new \Exception("TODO");
    }

    /**
     * @param Record $record
     *
     * @return int
     */
    private static function score(Record $record)
    {
        $acc = 0;

        foreach($record->getRounds() as $recordRound) {
            $round = $recordRound->getRound();

            foreach($round->getTargets() as $roundTarget) {
                $distance = Unit::convert($roundTarget->getDistanceValue(), $roundTarget->getDistanceUnit(),Unit::METER);
                $size = Unit::convert($roundTarget->getTargetValue(), $roundTarget->getTargetUnit(),Unit::CENTIMETER);

                $acc += $distance*$size;
            }
        }

        return $acc;
    }
}
