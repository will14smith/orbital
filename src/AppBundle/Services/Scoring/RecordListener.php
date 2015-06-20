<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Services\Events\ScoreEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RecordListener {
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function handleScore(ScoreEvent $event)
    {

    }
}