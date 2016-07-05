<?php

namespace AppBundle\Tests\Services\Handicap;

use AppBundle\Entity\Score;
use AppBundle\Services\Handicap\HandicapIdentifier;
use AppBundle\Services\Handicap\ReassessmentRepositoryInterface;

class NullReassessmentRepository implements ReassessmentRepositoryInterface
{
    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $start_date
     * @param \DateTime          $end_date
     *
     * @return Score[]
     *
     * @throws \Exception
     */
    public function getScores(HandicapIdentifier $id, \DateTime $start_date, \DateTime $end_date)
    {
        throw new \Exception('This should not be called');
    }
}
