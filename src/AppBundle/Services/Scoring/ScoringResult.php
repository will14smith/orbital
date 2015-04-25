<?php


namespace AppBundle\Services\Scoring;


use AppBundle\Entity\Score;

class ScoringResult {
    /** @var int */
    private $total;
    /** @var int */
    private $golds;
    /** @var int */
    private $hits;
    /** @var int */
    private $arrows;

    public function __construct($total, $golds, $hits, $arrows) {
        if(!is_int($total)) {
            throw new \InvalidArgumentException();
        }
        if(!is_int($golds)) {
            throw new \InvalidArgumentException();
        }
        if(!is_int($hits)) {
            throw new \InvalidArgumentException();
        }
        if(!is_int($arrows)) {
            throw new \InvalidArgumentException();
        }

        $this->total = $total;
        $this->golds = $golds;
        $this->hits = $hits;
        $this->arrows = $arrows;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getGolds()
    {
        return $this->golds;
    }

    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @return int
     */
    public function getArrows()
    {
        return $this->arrows;
    }

    public function applyToScore(Score $score)
    {
        $score->setScore($this->getTotal());
        $score->setGolds($this->getGolds());
        $score->setHits($this->getHits());
    }

    /**
     * @param ScoringResult $a
     * @param ScoringResult $b
     * @return ScoringResult
     */
    public static function add(ScoringResult $a, ScoringResult $b)
    {
        return new ScoringResult(
          $a->getTotal() + $b->getTotal(),
          $a->getGolds() + $b->getGolds(),
          $a->getHits() + $b->getHits(),
          $a->getArrows() + $b->getArrows()
        );
    }
}
