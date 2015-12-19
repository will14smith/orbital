<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
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

            switch($match[1]) {
                case 'm': $table[Gender::MALE] = [BowType::RECURVE => $value, BowType::BAREBOW => $value, BowType::LONGBOW => $value, BowType::COMPOUND => $value]; break;
                case 'f': $table[Gender::FEMALE] = [BowType::RECURVE => $value, BowType::BAREBOW => $value, BowType::LONGBOW => $value, BowType::COMPOUND => $value]; break;

                case 'r': $table[Gender::MALE][BowType::RECURVE] = $value; $table[Gender::FEMALE][BowType::RECURVE] = $value; break;
                case 'b': $table[Gender::MALE][BowType::BAREBOW] = $value; $table[Gender::FEMALE][BowType::BAREBOW] = $value; break;
                case 'l': $table[Gender::MALE][BowType::LONGBOW] = $value; $table[Gender::FEMALE][BowType::LONGBOW] = $value; break;
                case 'c': $table[Gender::MALE][BowType::COMPOUND] = $value; $table[Gender::FEMALE][BowType::COMPOUND] = $value; break;

                case 'mr': $table[Gender::MALE][BowType::RECURVE] = $value; break;
                case 'mb': $table[Gender::MALE][BowType::BAREBOW] = $value; break;
                case 'ml': $table[Gender::MALE][BowType::LONGBOW] = $value; break;
                case 'mc': $table[Gender::MALE][BowType::COMPOUND] = $value; break;

                case 'fr': $table[Gender::FEMALE][BowType::RECURVE] = $value; break;
                case 'fb': $table[Gender::FEMALE][BowType::BAREBOW] = $value; break;
                case 'fl': $table[Gender::FEMALE][BowType::LONGBOW] = $value; break;
                case 'fc': $table[Gender::FEMALE][BowType::COMPOUND] = $value; break;
            }
        }

        $this->handicaps = $table;
    }
}