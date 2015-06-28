<?php

namespace AppBundle\Services\Competitions;

interface ICompetitionResult {
    /**
     * @return boolean
     */
    public function isTeam();

    /**
     * @return \AppBundle\Entity\Club
     */
    public function getClub();

    /**
     * @return int
     */
    public function getScoreValue();
    /**
     * @return int
     */
    public function getGolds();
    /**
     * @return int
     */
    public function getHits();

    /**
     * @return int
     */
    public function getPosition();
    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return boolean
     */
    public function isHighPrecision();
    /**
     * @param boolean $precision
     */
    public function setHighPrecision($precision);
}