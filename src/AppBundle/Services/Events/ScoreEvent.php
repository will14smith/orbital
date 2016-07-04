<?php

namespace AppBundle\Services\Events;

use AppBundle\Entity\Score;
use Symfony\Component\EventDispatcher\Event;

class ScoreEvent extends Event
{
    /**
     * @var Score
     */
    private $score;

    public function __construct(Score $score)
    {
        $this->score = $score;
    }

    /**
     * @return Score
     */
    public function getScore()
    {
        return $this->score;
    }
}
