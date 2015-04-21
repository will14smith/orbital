<?php


namespace AppBundle\Services\Events;

use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DoctrineListener
{
    /** @var EventDispatcher */
    private $event_dispatcher;

    public function __construct($event_dispatcher)
    {
        $this->event_dispatcher = $event_dispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Score) {
            $event = new ScoreEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_create', $event);
        } else if ($entity instanceof LeagueMatch) {
            $event = new LeagueMatchEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.match_create', $event);
        } else if ($entity instanceof ScoreArrow) {
            $event = new ScoreArrowEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_arrow_create', $event);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Score) {
            $event = new ScoreEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_update', $event);
        } else if ($entity instanceof LeagueMatch) {
            $event = new LeagueMatchEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.match_update', $event);
        } else if ($entity instanceof ScoreArrow) {
            $event = new ScoreArrowEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_arrow_update', $event);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof ScoreArrow) {
            $event = new ScoreArrowEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_arrow_remove', $event);
        }
    }

    public function postFlush(PostFlushEventArgs $args){

    }
}
