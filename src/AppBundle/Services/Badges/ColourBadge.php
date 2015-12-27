<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Handicap\HandicapCalculator;

class ColourBadge
{
    /**
     * @var Badge
     */
    private $badge;

    /**
     * @var int[][]
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
     * NOTE: this assumes that [m][r] is representative
     *
     * @param ColourBadge|null $other
     * @return bool
     */
    public function isBetterThan(ColourBadge $other = null)
    {
        if(!$other) { return true; }

        if(!$this->handicaps) { $this->buildHandicapTable(); }

        return $this->handicaps[Gender::MALE][BowType::RECURVE] < $other->handicaps[Gender::MALE][BowType::RECURVE];
    }

    /**
     * @param Score $score
     * @return bool
     */
    public function isLowerThan(Score $score)
    {
        if(!$this->handicaps) { $this->buildHandicapTable(); }

        $scoreHandicap = (new HandicapCalculator)->handicapForScore($score);

        $gender = $score->getPerson()->getGender();
        $bowtype = $score->getBowtype();

        return $scoreHandicap <= $this->handicaps[$gender][$bowtype];
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


    private static $tableFill = [
        'm' => [Gender::MALE => [BowType::RECURVE, BowType::BAREBOW, BowType::LONGBOW, BowType::COMPOUND]],
        'f' => [Gender::FEMALE => [BowType::RECURVE, BowType::BAREBOW, BowType::LONGBOW, BowType::COMPOUND]],

        'r' => [Gender::MALE => [BowType::RECURVE], Gender::FEMALE => [BowType::RECURVE]],
        'b' => [Gender::MALE => [BowType::BAREBOW], Gender::FEMALE => [BowType::BAREBOW]],
        'l' => [Gender::MALE => [BowType::LONGBOW], Gender::FEMALE => [BowType::LONGBOW]],
        'c' => [Gender::MALE => [BowType::COMPOUND], Gender::FEMALE => [BowType::COMPOUND]],

        'mr' => [Gender::MALE => [BowType::RECURVE]],
        'mb' => [Gender::MALE => [BowType::BAREBOW]],
        'ml' => [Gender::MALE => [BowType::LONGBOW]],
        'mc' => [Gender::MALE => [BowType::COMPOUND]],

        'fr' => [Gender::FEMALE => [BowType::RECURVE]],
        'fb' => [Gender::FEMALE => [BowType::BAREBOW]],
        'fl' => [Gender::FEMALE => [BowType::LONGBOW]],
        'fc' => [Gender::FEMALE => [BowType::COMPOUND]],
    ];

    private function buildHandicapTable()
    {
        // default table
        $table = [
            Gender::MALE => [
                BowType::RECURVE => 0,
                BowType::BAREBOW => 0,
                BowType::LONGBOW => 0,
                BowType::COMPOUND => 0,
            ],
            Gender::FEMALE => [
                BowType::RECURVE => 0,
                BowType::BAREBOW => 0,
                BowType::LONGBOW => 0,
                BowType::COMPOUND => 0,
            ],
        ];

        $param = substr($this->badge->getAlgoName(), strlen(ColoursHandler::IDENT .  ':'));
        preg_match_all('/(?:^|,)(..?)-(\d+)/', $param, $matches, PREG_SET_ORDER);

        foreach($matches as $match) {
            $value = intval($match[2]);

            $fill = self::$tableFill[$match[1]];
            //TODO log this
            if(!$fill) { continue; }

            foreach($fill as $gender => $types) {
                foreach($types as $type) {
                    $table[$gender][$type] = $value;
                }
            }
        }

        $this->handicaps = $table;
    }
}