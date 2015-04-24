<?php


namespace AppBundle\Services\Scoring;


use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\ScoreArrow;

class TargetArrows {
    /** @var RoundTarget */
    private $target;
    /** @var ScoreArrow[] */
    private $arrows;

    public function __construct($target, $arrows) {
        if($target == null) {
            throw new \InvalidArgumentException();
        }
        if($arrows == null) {
            throw new \InvalidArgumentException();
        }

        $this->target = $target;
        $this->arrows = $arrows;
    }

    /**
     * @return RoundTarget
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return ScoreArrow[]
     */
    public function getArrows()
    {
        return $this->arrows;
    }
}
