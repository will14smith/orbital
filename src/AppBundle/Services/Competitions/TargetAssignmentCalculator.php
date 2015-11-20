<?php

namespace AppBundle\Services\Competitions;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;

class TargetAssignmentCalculator
{
    /** @var Competition */
    private $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function calculate()
    {
        foreach ($this->competition->getSessions() as $session) {
            $this->calculateSession($session);
        }
    }

    private function calculateSession(CompetitionSession $session)
    {
        $boss = 1;
        $target = 1;

        $bosses = $session->getBossCount();
        $targets = $session->getTargetCount() * $session->getDetailCount();

        foreach ($session->getEntries() as $entry) {
            $entry->setBossNumber($boss);
            $entry->setTargetNumber($target);

            $target++;

            if($target > $targets) {
                $target = 1;
                $boss++;

                if($boss > $bosses) {
                    throw new \Exception("Ran out of space!");
                }
            }
        }
    }
}