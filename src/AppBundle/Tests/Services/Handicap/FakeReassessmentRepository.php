<?php

namespace AppBundle\Tests\Services\Handicap;

use AppBundle\Entity\Score;
use AppBundle\Services\Handicap\HandicapIdentifier;
use AppBundle\Services\Handicap\ReassessmentRepositoryInterface;

class FakeReassessmentRepository implements ReassessmentRepositoryInterface
{
    /**
     * @var Score[]
     */
    private $scores;

    /**
     * @param Score[] $scores
     */
    public function __construct(array $scores)
    {

        $this->scores = $scores;
    }

    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $start_date
     * @param \DateTime          $end_date
     *
     * @return Score[]
     */
    public function getScores(HandicapIdentifier $id, \DateTime $start_date, \DateTime $end_date)
    {
        // NOTE: this doesn't filter, but it could...

        return $this->scores;
    }
}
