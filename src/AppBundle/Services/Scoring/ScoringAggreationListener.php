<?php

namespace AppBundle\Services\Scoring;

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
        //TODO get round target...

        //TODO update attach score (total, hits, golds)
    }

    public function arrowUpdated(ScoreArrowEvent $event)
    {
        throw new \Exception("Not Implemented");
    }

    public function arrowRemoved(ScoreArrowEvent $event)
    {
        throw new \Exception("Not Implemented");
    }
}