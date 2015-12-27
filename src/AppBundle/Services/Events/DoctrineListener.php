<?php


namespace AppBundle\Services\Events;

use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\Score;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DoctrineListener
{
    /** @var EventDispatcher */
    private $dispatcher;

    /**
     * @param $dispatcher
     */
    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    //

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Score) {
            $event = new ScoreEvent($entity);
            $this->dispatcher->dispatch('orbital.events.score_create', $event);
        } else if ($entity instanceof LeagueMatch) {
            $event = new LeagueMatchEvent($entity);
            $this->dispatcher->dispatch('orbital.events.match_create', $event);
        } else if($entity instanceof RecordHolderPerson) {
            $event = new RecordHolderPersonEvent($entity);
            $this->dispatcher->dispatch('orbital.events.record_holder_person_create', $event);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Score) {
            $event = new ScoreEvent($entity);
            $this->dispatcher->dispatch('orbital.events.score_update', $event);
        } else if ($entity instanceof LeagueMatch) {
            $event = new LeagueMatchEvent($entity);
            $this->dispatcher->dispatch('orbital.events.match_update', $event);
        } else if($entity instanceof RecordHolderPerson) {
            $event = new RecordHolderPersonEvent($entity);
            $this->dispatcher->dispatch('orbital.events.record_holder_person_update', $event);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        // $entity = $args->getEntity();
    }

    public function postFlush(PostFlushEventArgs $args){

    }
}
