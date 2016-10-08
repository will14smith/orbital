<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Handicap\Classifications\BaseClassifications;
use AppBundle\Services\Handicap\Classifications\FemaleAdultClassifications;
use AppBundle\Services\Handicap\Classifications\MaleAdultClassifications;

class ClassificationCalculator
{
    private static $classifications = [
        Classification::GRAND_MASTER_BOWMAN,
        Classification::MASTER_BOWMAN,
        Classification::BOWMAN,
        Classification::FIRST,
        Classification::SECOND,
        Classification::THIRD,
    ];

    /**
     * @var HandicapCalculator
     */
    private $calculator;

    public function __construct(HandicapCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @param string $gender
     * @param int    $age
     *
     * @return BaseClassifications
     * @throws \Exception
     */
    private function getClassificationTable(string $gender, int $age)
    {
        if ($age < 18) {
            throw new \Exception("NotImplemented: Junior Classifications");
        }

        if ($gender == Gender::MALE) {
            return new MaleAdultClassifications();
        } else {
            return new FemaleAdultClassifications();
        }
    }

    public function calculateRoundScore(Round $round, string $gender, int $age, string $bowtype, string $classification)
    {
        $handicap = $this->getClassificationTable($gender, $age)->getHandicap($bowtype, $classification);

        $score = $this->calculator->score($round, $bowtype == BowType::COMPOUND, $handicap);

        return $score;
    }

    /**
     * @param RoundTarget $target
     * @param string      $gender
     * @param int         $age
     * @param string      $bowtype
     * @param string      $classification
     *
     * @return mixed
     */
    public function calculateTargetScore(RoundTarget $target, string $gender, int $age, string $bowtype, string $classification)
    {
        $handicap = $this->getClassificationTable($gender, $age)->getHandicap($bowtype, $classification);

        $score = $this->calculator->scoreTarget($target, $bowtype == BowType::COMPOUND, $handicap);

        return $score;
    }

    /**
     * @param Score $score
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function calculateClassification(Score $score)
    {
        $round = $score->getRound();
        $person = $score->getPerson();
        $gender = $person->getGender();
        $age = $person->getAge($score->getDateShot());
        $bowtype = $score->getBowtype();

        foreach (self::$classifications as $classification) {
            if (!$this->isValidClassifiation($round, $gender, $age, $classification)) {
                return null;
            }

            $min_required_score = $this->calculateRoundScore($round, $gender, $age, $bowtype, $classification);

            if ($score->getScore() >= $min_required_score) {
                return $classification;
            }
        }

        return null;
    }

    /**
     * @param Round  $round
     * @param string $gender
     * @param int    $age
     * @param string $classification
     *
     * @return bool
     */
    public function isValidClassifiation(Round $round, string $gender, int $age, string $classification)
    {
        $max_classification = $this->getClassificationTable($gender, $age)->getMaxClassification($round);
        if ($max_classification === $classification) {
            return true;
        }

        $classification_index = array_search($classification, self::$classifications);
        if ($classification_index === false) {
            return false;
        }

        $max_classification_index = array_search($max_classification, self::$classifications);

        return $max_classification_index < $classification_index;
    }
}
