<?php

namespace AppBundle\Services\Events;

use AppBundle\Entity\RecordHolderPerson;
use Symfony\Component\EventDispatcher\Event;

class RecordHolderPersonEvent extends Event
{
    /**
     * @var RecordHolderPerson
     */
    private $rhp;

    /**
     * @param RecordHolderPerson $rhp
     */
    public function __construct(RecordHolderPerson $rhp)
    {
        $this->rhp = $rhp;
    }

    /**
     * @return RecordHolderPerson
     */
    public function getRecordHolderPerson()
    {
        return $this->rhp;
    }
}
