<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CompetitionResultsManager
{
    /** @var Registry */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Competition $competition
     * @param CompetitionSession $session
     *
     * @return CompetitionResult[]
     */
    public function getResults(Competition $competition, CompetitionSession $session = null)
    {
        $entryRepository = $this->doctrine->getRepository('AppBundle:CompetitionSessionEntry');

        if ($session === null) {
            $qb = $entryRepository->findByCompetition($competition);
        } else {
            $qb = $entryRepository->findBySession($session);
        }

        $qb->join('e.score', 'score')
            ->orderBy('score.score', 'DESC')
            ->addOrderBy('score.golds', 'DESC')
            ->addOrderBy('score.hits', 'DESC');

        /** @var CompetitionSessionEntry[] $dbResults */
        $dbResults = $qb->getQuery()->getResult();
        $results = [];

        $position = 1;

        $count = count($dbResults);
        for ($i = 0; $i < $count; $i++) {
            $result = $dbResults[$i];
            $prevResult = $i > 0 ? $dbResults[$i - 1] : null;
            $nextResult = $i + 1 < $count ? $dbResults[$i + 1] : null;

            $results[] = $this->buildResult($result, $position, $prevResult, $nextResult);
        }

        return $results;
    }

    private function buildResult(CompetitionSessionEntry $dbResult, &$position,
                                 CompetitionSessionEntry $dbPrevious = null, CompetitionSessionEntry $dbNext = null)
    {
        $score = $dbResult->getScore();

        // next has same score (all metrics)
        $draw = false;
        // prev / next has same score
        $highPrecision = false;

        if ($dbNext !== null) {
            $nextScore = $dbNext->getScore();

            if ($score->getScore() === $nextScore->getScore()) {
                $highPrecision = true;

                if ($score->getGolds() === $nextScore->getGolds()
                    && $score->getHits() === $nextScore->getHits()
                ) {
                    $draw = true;
                }
            }
        }
        if ($dbPrevious !== null) {
            $prevScore = $dbPrevious->getScore();

            if ($score->getScore() === $prevScore->getScore()) {
                $highPrecision = true;
            }
        }

        $result = new CompetitionResult($dbResult, $position, $highPrecision);

        if (!$draw) {
            $position++;
        }

        return $result;
    }
}