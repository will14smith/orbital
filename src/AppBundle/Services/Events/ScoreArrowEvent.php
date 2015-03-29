<?php


namespace AppBundle\Services\Events;


use AppBundle\Entity\ScoreArrow;
use Symfony\Component\EventDispatcher\Event;

class ScoreArrowEvent extends Event
{
    /**
     * @var ScoreArrow
     */
    private $arrow;

    public function __construct(ScoreArrow $arrow)
    {

        $this->arrow = $arrow;
    }

    /**
     * @return ScoreArrow
     */
    public function getArrow()
    {
        return $this->arrow;
    }
}