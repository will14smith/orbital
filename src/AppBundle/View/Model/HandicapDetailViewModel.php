<?php

namespace AppBundle\View\Model;

use AppBundle\Entity\PersonHandicap;
use AppBundle\Services\Handicap\HandicapIdentifier;

class HandicapDetailViewModel
{
    /**
     * @var HandicapIdentifier
     */
    private $id;
    /**
     * @var PersonHandicap
     */
    private $current;
    /**
     * @var PersonHandicap[]
     */
    private $historic;

    /**
     * @param HandicapIdentifier $id
     * @param PersonHandicap     $current
     * @param PersonHandicap[]   $historic
     */
    public function __construct(HandicapIdentifier $id, PersonHandicap $current, array $historic)
    {
        $this->id = $id;
        $this->current = $current;
        $this->historic = $historic;
    }

    /**
     * @return HandicapIdentifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PersonHandicap
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @return PersonHandicap[]
     */
    public function getHistoric()
    {
        return $this->historic;
    }
}
