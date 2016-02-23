<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Handicap\HandicapCalculator;

class ColourBadge
{
    /**
     * @var Badge
     */
    private $badge;

    /**
     * @var int[][][]
     */
    private $handicaps;

    /**
     * ColourBadge constructor.
     * @param Badge $badge
     */
    public function __construct($badge)
    {
        $this->badge = $badge;
    }

    /**
     * NOTE: this assumes that [o][m][r] is representative
     *
     * @param ColourBadge|null $other
     * @return bool
     */
    public function isBetterThan(ColourBadge $other = null)
    {
        if (!$other) {
            return true;
        }

        if (!$this->handicaps) {
            $this->buildHandicapTable();
        }

        return $this->handicaps[Environment::OUTDOOR][Gender::MALE][BowType::RECURVE] < $other->handicaps[Environment::OUTDOOR][Gender::MALE][BowType::RECURVE];
    }

    /**
     * @param Score $score
     * @return bool
     */
    public function isLowerThan(Score $score)
    {
        if (!$this->handicaps) {
            $this->buildHandicapTable();
        }

        $scoreHandicap = (new HandicapCalculator)->handicapForScore($score);

        $gender = $score->getPerson()->getGender();
        $bowtype = $score->getBowtype();
        $indoor = +$score->getRound()->getIndoor();

        $table = $this->handicaps;
        if(!array_key_exists($indoor, $table)) {
            return false;
        }
        $table = $table[$indoor];
        if(!array_key_exists($gender, $table)) {
            return false;
        }
        $table = $table[$gender];
        if(!array_key_exists($bowtype, $table)) {
            return false;
        }
        $table = $table[$bowtype];

        return $scoreHandicap <= $table;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->badge->getId();
    }

    /**
     * @return Badge
     */
    public function getBadge()
    {
        return $this->badge;
    }


    /**
     * @var array
     */
    private static $tableFill;

    public static function initFillTable()
    {
        $envs = ['i' => [Environment::INDOOR], 'o' => [Environment::OUTDOOR], '' => array_keys(Environment::$choices)];
        $genders = ['m' => [Gender::MALE], 'f' => [Gender::FEMALE], '' => array_keys(Gender::$choices)];
        $bowTypes = [
            'r' => [BowType::RECURVE],
            'b' => [BowType::BAREBOW],
            'l' => [BowType::LONGBOW],
            't' => [BowType::TRADITIONAL],
            'c' => [BowType::COMPOUND],
            '' => array_keys(BowType::$choices)
        ];

        self::$tableFill = [];

        foreach ($envs as $envKey => $envValues) {
            foreach ($genders as $genderKey => $genderValues) {
                foreach ($bowTypes as $bowTypeKey => $bowTypeValues) {
                    $key = $envKey . $genderKey . $bowTypeKey;
                    $values = self::crossArrays($envValues, $genderValues, $bowTypeValues);

                    self::$tableFill[$key] = $values;
                }
            }
        }
    }

    private static function crossArrays()
    {
        $count = func_num_args();
        $arrays = func_get_args();

        if ($count == 0) {
            return [];
        }
        if ($count == 1) {
            return $arrays[0];
        }

        $head = $arrays[0];
        $tail = array_slice($arrays, 1);

        $result = [];
        foreach ($head as $key) {
            $result[$key] = call_user_func_array([__NAMESPACE__ .'\ColourBadge', 'crossArrays'], $tail);
        }
        return $result;
    }

    private function buildHandicapTable()
    {
        /** @var int[][][] $table */
        $table = [];

        $param = substr($this->badge->getAlgoName(), strlen(ColoursHandler::IDENT . ':'));
        preg_match_all('/(?:^|,)(..?)-(\d+)/', $param, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $value = intval($match[2]);

            $fill = self::$tableFill[$match[1]];
            //TODO log this
            if (!$fill) {
                continue;
            }

            foreach ($fill as $env => $genders) {
                if (!array_key_exists($env, $table)) {
                    $table[$env] = [];
                }
                foreach ($genders as $gender => $types) {
                    if (!array_key_exists($gender, $table[$env])) {
                        $table[$env][$gender] = [];
                    }
                    foreach ($types as $type) {
                        if (!array_key_exists($type, $table[$env][$gender])) {
                            $table[$env][$gender][$type] = $value;
                        }
                    }
                }
            }
        }

        $this->handicaps = $table;
    }
}

ColourBadge::initFillTable();