<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\CompetitionSessionEntry;

class CompetitionResult {
    /** @var CompetitionSessionEntry */
    private $entry;
    /** @var integer */
    private $position;
    /** @var bool */
    private $highPrecision;

    /**
     * @param CompetitionSessionEntry $entry
     * @param integer $position
     * @param bool $highPrecision should show Golds + Hits
     */
    public function __construct(CompetitionSessionEntry $entry, $position, $highPrecision = false) {
        $this->entry = $entry;
        $this->position = $position;
        $this->highPrecision = $highPrecision;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return \AppBundle\Entity\Person
     */
    public function getPerson() {
        return $this->entry->getPerson();
    }

    /**
     * @return \AppBundle\Entity\Club
     */
    public function getClub() {
        return $this->entry->getClub();
    }

    /**
     * @return \AppBundle\Entity\Score
     */
    public function getScore() {
        return $this->entry->getScore();
    }

    /**
     * @return boolean
     */
    public function isHighPrecision()
    {
        return $this->highPrecision;
    }
}