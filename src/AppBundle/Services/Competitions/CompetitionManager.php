<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;

class CompetitionManager
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
     * @param CompetitionSession $session
     * @param Person $person
     *
     * @return bool
     */
    public static function hasEntered(CompetitionSession $session, Person $person)
    {
        foreach($session->getEntries() as $entry) {
            if($entry->getId() === $person->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param CompetitionSession $session
     *
     * @return int
     */
    public static function getTotalSpaces(CompetitionSession $session)
    {
        return $session->getBossCount()
        * $session->getTargetCount()
        * $session->getDetailCount();
    }

    /**
     * @param CompetitionSession $session
     * @return int
     */
    public static function getFilledSpaces(CompetitionSession $session)
    {
        return $session->getEntries()->filter(function(CompetitionSessionEntry $entry) {
            return $entry->getDateApproved() !== null;
        })->count();
    }

    /**
     * @param CompetitionSession $session
     *
     * @return int
     */
    public static function getFreeSpaces(CompetitionSession $session)
    {
        return self::getTotalSpaces($session) - self::getFilledSpaces($session);
    }

    public static function assignTargets(Competition $competition)
    {
        $calculator = new TargetAssignmentCalculator($competition);

        $calculator->calculate();
    }

    public static function startSession(ObjectManager $em, CompetitionSession $session)
    {
        $session->setActualStartTime(new \DateTime());
        $session->setActualEndTime(null);

        foreach($session->getEntries() as $entry) {
            if($entry->getScore() === null) {
                $score = self::createScore($entry);

                $entry->setScore($score);
                $em->persist($score);
            }
        }

        $em->flush();
    }

    private static function createScore(CompetitionSessionEntry $entry)
    {
        $score = new Score();

        $score->setPerson($entry->getPerson());
        $score->setSkill($entry->getSkill());
        $score->setBowtype($entry->getBowtype());

        $score->setRound($entry->getRound());

        $score->setScore(0);
        $score->setGolds(0);
        $score->setHits(0);

        $score->setCompetition(true);
        $score->setComplete(false);

        $score->setDateShot($entry->getSession()->getActualStartTime());

        return $score;
    }
}
