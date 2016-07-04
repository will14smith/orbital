<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use AppBundle\Services\Events\RecordHolderPersonEvent;
use AppBundle\Services\Events\ScoreEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;

class BadgeListener
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param ScoreEvent $event
     */
    public function handle_score(ScoreEvent $event)
    {
        (new ColoursHandler($this->doctrine, $this->getBadges(ColoursHandler::IDENT)))->handle($event->getScore());
        (new ExpressionHandler($this->doctrine, $this->getBadges(ExpressionHandler::IDENT)))->handle($event->getScore());
    }

    /**
     * @param RecordHolderPersonEvent $event
     */
    public function handle_record_holder_person(RecordHolderPersonEvent $event)
    {
        (new RecordHandler($this->doctrine, $this->getBadges(RecordHandler::IDENT)))->handle($event->getRecordHolderPerson());
    }

    /**
     * @param string $ident
     *
     * @return Badge[]
     */
    private function getBadges($ident)
    {
        return $this->doctrine->getRepository('AppBundle:Badge')->findByAlgorithm($ident);
    }
}
