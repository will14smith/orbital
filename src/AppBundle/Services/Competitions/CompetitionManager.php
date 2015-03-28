<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use Doctrine\Bundle\DoctrineBundle\Registry;

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

    public function assignTargets(Competition $competition)
    {
        //TODO make strategy pattern
        $em = $this->doctrine->getManager();

        $target = 1;
        $boss = 1;

        foreach ($competition->getEntries() as $entry) {
            $entry->setBossNumber($boss);
            $entry->setTargetNumber($target);

            $boss++;
            if ($boss > $competition->getBossCount()) {
                $boss = 1;
                $target++;
            }
        }

        $em->flush();
    }
}