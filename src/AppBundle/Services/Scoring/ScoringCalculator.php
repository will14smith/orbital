<?php

namespace AppBundle\Services\Scoring;


use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;

class ScoringCalculator
{
    /**
     * Updates the score object (doesn't persist)
     *
     * @param Score $score
     */
    public static function updateStats(Score &$score)
    {
        $arrows = $score->getArrows();

        $round = $score->getRound();
        $targets = $round->getTargets();

        $mappedTargets = self::getArrowsByTarget($targets, $arrows);
        $results = array_map(function(TargetArrows $mappedTarget) {
            return self::mapArrowsToResult($mappedTarget->getTarget(), $mappedTarget->getArrows());
        }, $mappedTargets);

        return array_reduce($results, function($acc, $x) {
           return self::reduceScoringResults($acc, $x);
        }, new ScoringResult(0, 0, 0));
    }

    /**
     * @param RoundTarget[] $targets
     * @param ScoreArrow[] $arrows
     * @return TargetArrows[]
     */
    public static function getArrowsByTarget($targets, $arrows)
    {
        $result = [];

        $offset = 0;
        foreach ($targets as $target) {
            $count = $target->getArrowCount();
            $arrows = array_slice($arrows, $offset, $count);
            $offset += $count;

            $result[] = new TargetArrows($target, $arrows);
        }

        return $result;
    }

    /**
     * @param RoundTarget $target
     * @param ScoreArrow[] $arrows
     * @return ScoringResult
     */
    public static function mapArrowsToResult($target, $arrows)
    {
        $calc = ZoneManager::get($target->getScoringZones());

        $total = 0;
        $golds = 0;
        $hits = 0;

        foreach ($arrows as $arrow) {
            $total += $calc->getValue($arrow);
            $golds += $calc->isGold($arrow) ? 1 : 0;
            $hits += $calc->isHit($arrow) ? 1 : 0;
        }

        return new ScoringResult($total, $golds, $hits);
    }

    /**
     * @param ScoringResult $a
     * @param ScoringResult $b
     * @return ScoringResult
     */
    public static function reduceScoringResults(ScoringResult $a, ScoringResult $b) {
        $total = $a->getTotal() + $b->getTotal();
        $golds = $a->getGolds() + $b->getGolds();
        $hits = $a->getHits() + $b->getHits();

        return new ScoringResult($total, $golds, $hits);
    }
}
