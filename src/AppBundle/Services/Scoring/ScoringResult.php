<?php


namespace AppBundle\Services\Scoring;


class ScoringResult {
    /** @var int */
    private $total;
    /** @var int */
    private $golds;
    /** @var int */
    private $hits;

    public function __construct($total, $golds, $hits) {
        if(!is_int($total)) {
            throw new \InvalidArgumentException();
        }
        if(!is_int($golds)) {
            throw new \InvalidArgumentException();
        }
        if(!is_int($hits)) {
            throw new \InvalidArgumentException();
        }

        $this->total = $total;
        $this->golds = $golds;
        $this->hits = $hits;
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
}
