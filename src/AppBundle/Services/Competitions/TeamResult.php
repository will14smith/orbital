<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Club;

class TeamResult implements ICompetitionResult
{
    /** @var ICompetitionResult[] */
    private $results;
    /** @var int */
    private $position;
    /** @var boolean */
    private $highPrecision;

    /**
     * @param ICompetitionResult[] $results
     */
    public function __construct(array $results)
    {

        $this->results = $results;
    }

    /**
     * @return boolean
     */
    public function isTeam() {
        return true;
    }

    /**
     * @return ICompetitionResult[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return \AppBundle\Entity\Club
     */
    public function getClub()
    {
        // assuming all the clubs are the same.
        return $this->results[0]->getClub();
    }

    /**
     * @return int
     */
    public function getScoreValue()
    {
        return array_reduce($this->results, function ($acc, ICompetitionResult $result) {
            return $acc + $result->getScoreValue();
        }, 0);
    }

    /**
     * @return int
     */
    public function getGolds()
    {
        return array_reduce($this->results, function ($acc, ICompetitionResult $result) {
            return $acc + $result->getGolds();
        }, 0);
    }

    /**
     * @return int
     */
    public function getHits()
    {
        return array_reduce($this->results, function ($acc, ICompetitionResult $result) {
            return $acc + $result->getHits();
        }, 0);
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
     * @param boolean $precision
     */
    public function setHighPrecision($precision)
    {
        $this->highPrecision = $precision;
    }
}