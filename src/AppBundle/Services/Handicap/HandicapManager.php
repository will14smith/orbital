<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\View\Model\HandicapDetailViewModel;
use Doctrine\Bundle\DoctrineBundle\Registry;

class HandicapManager
{
    public static $environments = [Environment::INDOOR, Environment::OUTDOOR];
    public static $bowTypes = [BowType::RECURVE, BowType::COMPOUND, BowType::BAREBOW, BowType::LONGBOW];

    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var HandicapDecider
     */
    private $decider;

    public function __construct(Registry $doctrine, HandicapDecider $decider)
    {
        $this->doctrine = $doctrine;
        $this->decider = $decider;
    }

    /**
     * @param Person $person
     *
     * @return HandicapDetailViewModel[]
     */
    public function getPersonDetails(Person $person)
    {
        $handicaps = [];

        foreach (self::$environments as $environment) {
            foreach (self::$bowTypes as $bowType) {
                $handicap = $this->getDetail(new HandicapIdentifier($person, $environment, $bowType));

                if ($handicap) {
                    $handicaps[] = $handicap;
                }
            }
        }

        return $handicaps;
    }

    /**
     * @param HandicapIdentifier $id
     *
     * @return HandicapDetailViewModel|null
     */
    private function getDetail(HandicapIdentifier $id)
    {
        $personHandicapRepository = $this->doctrine->getRepository('AppBundle:PersonHandicap');
        $current = $personHandicapRepository->findCurrent($id);

        if ($current === null) {
            return;
        }

        $historic = $personHandicapRepository->findById($id);

        return new HandicapDetailViewModel($id, $current, $historic);
    }

    /**
     * ASSUMPTION: $score is persisted.
     *
     * @param Score $score
     *
     * @return PersonHandicap[]
     */
    public function updateHandicap(Score $score)
    {
        $personHandicapRepository = $this->doctrine->getRepository('AppBundle:PersonHandicap');

        $id = new HandicapIdentifier($score->getPerson(), $score->isIndoor(), $score->getBowtype());
        $current_handicap = $personHandicapRepository->findCurrent($id);

        $new_handicaps = $this->buildHandicaps($id, $current_handicap, [$score]);

        $em = $this->doctrine->getManager();
        foreach ($new_handicaps as $handicap) {
            $em->persist($handicap);
        }
        $em->flush();

        return $new_handicaps;
    }

    /**
     * @param Person $person
     */
    public function rebuildPerson(Person $person)
    {
        foreach (self::$environments as $environment) {
            foreach (self::$bowTypes as $bowType) {
                $this->rebuild(new HandicapIdentifier($person, $environment, $bowType));
            }
        }
    }

    /**
     * @param HandicapIdentifier $id
     *
     * @return PersonHandicap[]
     */
    public function rebuild(HandicapIdentifier $id)
    {
        $em = $this->doctrine->getManager();

        // remove old handicaps
        /** @var PersonHandicap[] $handicaps */
        $handicaps = $this->doctrine->getRepository('AppBundle:PersonHandicap')->findById($id);

        foreach ($handicaps as $handicap) {
            $em->remove($handicap);
        }
        $em->flush();

        // rebuild from scores
        $scores = $this->doctrine->getRepository('AppBundle:Score')->getScoresByHandicapId($id);
        $new_handicaps = $this->buildHandicaps($id, null, $scores);

        foreach ($new_handicaps as $handicap) {
            $em->persist($handicap);
        }
        $em->flush();

        return $new_handicaps;
    }

    /**
     * @param HandicapIdentifier  $id
     * @param PersonHandicap|null $current_handicap
     * @param Score[]             $scores
     *
     * @return PersonHandicap[]
     */
    private function buildHandicaps(HandicapIdentifier $id, PersonHandicap $current_handicap = null, array $scores)
    {
        /** @var PersonHandicap[] $new_handicaps */
        $new_handicaps = [];

        while (count($scores) > 0) {
            $decision = $this->decider->decide($id, $current_handicap, $scores);
            $scores = $decision->getRemainingScores();

            if (!$decision->hasHandicap()) {
                continue;
            }

            $current_handicap = $decision->getHandicap();
            $new_handicaps[] = $current_handicap;
        }

        // final check for reassessment
        $decision = $this->decider->decide($id, $current_handicap, []);

        if ($decision->hasHandicap()) {
            $current_handicap = $decision->getHandicap();
            $new_handicaps[] = $current_handicap;
        }

        return $new_handicaps;
    }
}
