<?php


namespace AppBundle\Services\Events;

use AppBundle\Entity\Score;
use Doctrine\ORM\Event\LifecycleEventArgs;
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
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Score) {
            $event = new ScoreEvent($entity);
            $this->event_dispatcher->dispatch('orbital.events.score_update', $event);
        }
    }
}