<?php

namespace AppBundle\Services\Scoring;


use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;

class ScoringCalculator
{
    /**
     * Updates the score object (doesn't persist)
     *
     * @param Score $score
     */
    public static function updateStats(Score &$score)
    {
        $total = 0;
        $golds = 0;
        $hits = 0;

        $arrows = $score->getArrows();
        $arrow_count = count($arrows);

        $round = $score->getRound();
        $targets = $round->getTargets();

        /** @var RoundTarget $target */
        $target = $targets->current();
        $calc = ZoneManager::get($target->getScoringZones());

        $idx = 0;
        $local_idx = 0;
        while ($idx < $arrow_count) {
            if($local_idx >= $target->getArrowCount()) {
                $local_idx = 0;
                $target = $targets->next();
                $calc = ZoneManager::get($target->getScoringZones());
            }

            $total += $calc->getValue($arrows[$idx]);
            $golds += $calc->isGold($arrows[$idx]) ? 1 : 0;
            $hits += $calc->isHit($arrows[$idx]) ? 1 : 0;

            $idx++;
            $local_idx++;
        }

        $score->setScore($total);
        $score->setGolds($golds);
        $score->setHits($hits);
    }
}