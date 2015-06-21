<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Registry;

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
}
