<?php

namespace AppBundle\Services\Scoring;


use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;

class ScoringCalculator
{
    /**
     * @param Score $score
     * @return ScoringResult
     */
    public static function getScore(Score $score)
    {
        $arrows = $score->getArrows()->toArray();

        $round = $score->getRound();
        $targets = $round->getTargets();

        $mappedTargets = self::getArrowsByTarget($targets, $arrows);
        $results = array_map(function (TargetArrows $mappedTarget) {
            return self::mapArrowsToResult($mappedTarget->getTarget(), $mappedTarget->getArrows());
        }, $mappedTargets);

        return array_reduce($results, function ($acc, $x) {
            return ScoringResult::add($acc, $x);
        }, new ScoringResult(0, 0, 0, 0));
    }

    /**
     * @param RoundTarget[] $targets
     * @param ScoreArrow[] $arrows
     * @return TargetArrows[]
     */
    public static function getArrowsByTarget($targets, $arrows)
    {
        if($targets === null) {
            throw new \InvalidArgumentException();
        }
        if($arrows === null) {
            throw new \InvalidArgumentException();
        }

        $result = [];

        foreach ($targets as $target) {
            $count = $target->getArrowCount();
            $slice = array_splice($arrows, 0, $count);

            $result[] = new TargetArrows($target, $slice);
        }

        return $result;
    }

    /**
     * @param RoundTarget $target
     * @param ScoreArrow[] $arrows
     * @return ScoringResult
     */
    public static function mapArrowsToResult(RoundTarget $target, array $arrows)
    {
        $calc = ZoneManager::get($target->getScoringZones());

        $total = 0;
        $golds = 0;
        $hits = 0;

        $count = min($target->getArrowCount(), count($arrows));

        for($i = 0; $i < $count; $i++) {
            $arrow = $arrows[$i];

            $total += $calc->getValue($arrow);
            $golds += $calc->isGold($arrow) ? 1 : 0;
            $hits += $calc->isHit($arrow) ? 1 : 0;
        }

        return new ScoringResult($total, $golds, $hits, $count);
    }
}
