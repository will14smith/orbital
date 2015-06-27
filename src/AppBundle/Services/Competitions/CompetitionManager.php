<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;
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
        foreach ($session->getEntries() as $entry) {
            if ($entry->getId() === $person->getId()) {
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
        return $session->getEntries()->filter(function (CompetitionSessionEntry $entry) {
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

        foreach ($session->getEntries() as $entry) {
            if ($entry->getScore() === null) {
                $score = self::createScore($entry);

                $entry->setScore($score);
                $em->persist($score);
            }
        }

        $em->flush();
    }
    public static function endSession(ObjectManager $em, CompetitionSession $session)
    {
        $session->setActualEndTime(new \DateTime());

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

    /**
     * @param ObjectManager $em
     * @param Person $user
     * @param CompetitionSession $session
     * @param array $flushData
     * @return array
     */
    public static function handleFlush(ObjectManager $em, Person $user, CompetitionSession $session, $flushData)
    {
        $rejected = [];

        $entries = $session->getEntries();

        // loop through all bosses in $flushData
        foreach ($flushData as $bossNumber => $boss) {
            // loop through all targets in boss
            foreach ($boss as $targetNumber => $arrows) {
                // if entry || score is missing => add to rejected data
                $filteredEntries = $entries->filter(function (CompetitionSessionEntry $e) use ($bossNumber, $targetNumber) {
                    return $e->getBossNumber() === $bossNumber
                        && $e->getTargetNumber() === $targetNumber;
                });
                if ($filteredEntries->count() === 0) {
                    $rejected[] = [
                        'boss' => $bossNumber,
                        'target' => $targetNumber,
                        'arrows' => $arrows,
                        'message' => 'No entry found on target'
                    ];
                    continue;
                }

                /** @var CompetitionSessionEntry $entry */
                $entry = $filteredEntries->first();
                $score = $entry->getScore();

                if ($score === null) {
                    $rejected[] = [
                        'boss' => $bossNumber,
                        'target' => $targetNumber,
                        'arrows' => $arrows,
                        'message' => 'Entry has no associated score (have you started?)'
                    ];
                    continue;
                }

                //TODO if score is full => add to rejected data

                // else add arrows to score
                $index = $score->getArrows()->count();

                foreach ($arrows as $arrowScore) {
                    $arrow = new ScoreArrow();

                    $arrow->setScore($score);
                    $arrow->setNumber($index++);
                    $arrow->setValue($arrowScore);

                    $arrow->setInputBy($user);
                    $arrow->setInputTime(new \DateTime('now'));

                    $em->persist($arrow);
                }
            }
        }

        return $rejected;
    }
}
