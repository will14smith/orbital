<?php


namespace AppBundle\Services\Handicap;

use AppBundle\Services\Events\ScoreEvent;

class HandicapListener
{
    /**
     * @var HandicapManager
     */
    private $manager;

    public function __construct(HandicapManager $manager) {

        $this->manager = $manager;
    }

    public function score_create(ScoreEvent $event)
    {
        //TODO wait for acceptance

        $this->manager->updateHandicap($event->getScore());
    }

    public function score_update(ScoreEvent $event)
    {
        //TODO on accept / complete??

        throw new \Exception("TODO implement");
    }
}