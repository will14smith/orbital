<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ReassessmentRepository implements ReassessmentRepositoryInterface
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
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
        $scoreRepository = $this->doctrine->getRepository('AppBundle:Score');

        return $scoreRepository->getScoresByHandicapIdBetween($id, $start_date, $end_date);
    }
}
