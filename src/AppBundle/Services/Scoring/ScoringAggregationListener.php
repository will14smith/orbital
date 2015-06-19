<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Entity\Score;
use AppBundle\Services\Events\ScoreArrowEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ScoringAggregationListener
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function arrowAdded(ScoreArrowEvent $event)
    {
        $arrow = $event->getArrow();
        $score = $arrow->getScore();

        // HACK: the arrow can sometimes be out-of-sync with its score
        if(!$score->getArrows()->contains($arrow)) {
            $score->getArrows()->add($arrow);
        }

        $this->updateScore($score);
    }

    public function arrowUpdated(ScoreArrowEvent $event)
    {
        $this->updateScore($event->getArrow()->getScore());
    }

    public function arrowRemoved(ScoreArrowEvent $event)
    {
        $this->updateScore($event->getArrow()->getScore());
    }

    private function updateScore(Score $score)
    {
        $em = $this->doctrine->getManager();

        $stats = ScoringCalculator::getScore($score);
        $stats->applyToScore($score);

        $em->flush();
    }
}
