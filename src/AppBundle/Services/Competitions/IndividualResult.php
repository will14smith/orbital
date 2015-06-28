<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\CompetitionSessionEntry;

class IndividualResult implements ICompetitionResult
{
    /** @var CompetitionSessionEntry */
    private $entry;

    /** @var int */
    private $position;
    /** @var boolean */
    private $highPrecision;


    public function __construct(CompetitionSessionEntry $entry) {

        $this->entry = $entry;
    }

    public function isTeam() {
        return false;
    }

    /**
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->entry->getPerson();
    }

    /**
     * @return \AppBundle\Entity\Score
     */
    public function getScore()
    {
        return $this->entry->getScore();
    }

    /**
     * @return \AppBundle\Entity\Club
     */
    public function getClub()
    {
        return $this->entry->getClub();
    }

    /**
     * @return int
     */
    public function getScoreValue()
    {
        return $this->entry->getScore()->getScore();
    }
    /**
     * @return int
     */
    public function getGolds()
    {
        return $this->entry->getScore()->getGolds();
    }
    /**
     * @return int
     */
    public function getHits()
    {
        return $this->entry->getScore()->getHits();
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return boolean
     */
    public function isHighPrecision()
    {
        return $this->highPrecision;
    }

    /**
     * @param boolean $highPrecision
     */
    public function setHighPrecision($highPrecision)
    {
        $this->highPrecision = $highPrecision;
    }
}