<?php


namespace AppBundle\Services\Handicap;


use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Registry;

class HandicapManager
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function handleNewScore(Score $score)
    {
        //if (Hp == null && count(Pscores) >= 3) Hp = (Hs1 + Hs2 + Hs2) / 3
        //else if (Hs < Hp) Hp = ceil((Hp + Hs) / 2)

        // if Hp changed then record it
    }

    public function reassess(Person $person, $start_date = null)
    {
        if ($start_date == null) {
            $start_date = new \DateTime('1 year ago');
        }

        // take all scores since $start_date & compute HC
        // take highest 3 HC and average, this is the new HC

    }
}