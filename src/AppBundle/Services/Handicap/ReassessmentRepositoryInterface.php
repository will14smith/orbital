<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Score;

interface ReassessmentRepositoryInterface
{
    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $start_date
     * @param \DateTime          $end_date
     *
     * @return Score[]
     */
    public function getScores(HandicapIdentifier $id, \DateTime $start_date, \DateTime $end_date);
}
