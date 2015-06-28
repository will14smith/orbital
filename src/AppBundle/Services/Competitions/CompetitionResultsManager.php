<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Services\Enum\Skill;
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
     * @param array $filter
     *
     * @return ICompetitionResult[]
     */
    public function getResults(Competition $competition, CompetitionSession $session = null, array $filter = [])
    {
        if (array_key_exists('team', $filter) && $filter['team']) {
            $results = $this->getTeamEntries($competition, $session, $filter);
        } else {
            $results = $this->getEntries($competition, $session, $filter);
        }

        $position = 1;

        $count = count($results);
        for ($i = 0; $i < $count; $i++) {
            $result = $results[$i];
            $prevResult = $i > 0 ? $results[$i - 1] : null;
            $nextResult = $i + 1 < $count ? $results[$i + 1] : null;

            $this->calculateResult($result, $position, $prevResult, $nextResult);
        }

        return $results;
    }

    private function calculateResult(ICompetitionResult $result, &$position,
                                     ICompetitionResult $prevResult = null, ICompetitionResult $nextResult = null)
    {
        // next has same score (all metrics)
        $draw = false;
        // prev / next has same score (other metrics differ)
        $highPrecision = false;

        if ($nextResult !== null) {
            if ($result->getScoreValue() === $nextResult->getScoreValue()) {
                $highPrecision = true;

                if ($result->getGolds() === $nextResult->getGolds()
                    && $result->getHits() === $nextResult->getHits()
                ) {
                    $draw = true;
                }
            }
        }
        if ($prevResult !== null) {
            if ($result->getScoreValue() === $prevResult->getScoreValue()) {
                $highPrecision = true;
            }
        }

        $result->setPosition($position);
        $result->setHighPrecision($highPrecision);

        if (!$draw) {
            $position++;
        }
    }

    /**
     * @param Competition $competition
     * @param CompetitionSession $session
     * @param array $filter
     * @param bool $team
     *
     * @return IndividualResult[]
     */
    private function getEntries(Competition $competition, CompetitionSession $session = null, array $filter = [], $team = false)
    {
        $entryRepository = $this->doctrine->getRepository('AppBundle:CompetitionSessionEntry');

        if ($session === null) {
            $qb = $entryRepository->findByCompetition($competition);
        } else {
            $qb = $entryRepository->findBySession($session);
        }

        $qb = $qb->join('e.score', 'score')
            ->orderBy('score.score', 'DESC')
            ->addOrderBy('score.golds', 'DESC')
            ->addOrderBy('score.hits', 'DESC');

        if (array_key_exists('gender', $filter) && $filter['gender'] !== null) {
            $qb = $qb->andWhere('e.gender = :gender')
                ->setParameter('gender', $filter['gender']);
        }
        if (array_key_exists('skill', $filter) && $filter['skill'] !== null) {
            // senior teams can include novices.
            if (!$team || $filter['skill'] === Skill::NOVICE) {
                $qb = $qb->andWhere('e.skill = :skill')
                    ->setParameter('skill', $filter['skill']);
            }
        }
        if (array_key_exists('bowtype', $filter) && $filter['bowtype'] !== null && count($filter['bowtype']) > 0) {
            $qb = $qb->andWhere('e.bowtype IN (:bowtypes)')
                ->setParameter('bowtypes', $filter['bowtype']);
        }

        $entries = $qb->getQuery()->getResult();

        return array_map(function (CompetitionSessionEntry $entry) {
            return new IndividualResult($entry);
        }, $entries);
    }

    /**
     * @param Competition $competition
     * @param CompetitionSession $session
     * @param array $filter
     *
     * @return TeamResult[]
     */
    private function getTeamEntries(Competition $competition, CompetitionSession $session = null, array $filter = [])
    {
        $indEntries = $this->getEntries($competition, $session, $filter, true);

        //TODO
        $teamSize = 3;

        // aggregate ind results
        $teams = [];

        foreach ($indEntries as $entry) {
            $key = $entry->getClub()->getId();

            if(!array_key_exists($key, $teams)) {
                $teams[$key] = [];
            }

            if(count($teams[$key]) < $teamSize) {
                $teams[$key][] = $entry;
            }
        }

        // create results
        $results = [];

        foreach($teams as $teamEntries) {
            $results[] = new TeamResult($teamEntries);
        }

        usort($results, function(TeamResult $a, TeamResult $b) {
           return $b->getScoreValue() - $a->getScoreValue();
        });

        return $results;
    }
}