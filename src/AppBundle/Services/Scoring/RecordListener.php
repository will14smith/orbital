<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\Score;
use AppBundle\Services\Events\ScoreEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RecordListener
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function handleScore(ScoreEvent $event)
    {
        $score = $event->getScore();

        if ($score->getComplete() && $score->getCompetition()) {
            $this->checkForRecord($score);
        }
    }

    private function checkForRecord(Score $score)
    {
        $em = $this->doctrine->getManager();

        $recordRepository = $em->getRepository('AppBundle:Record');

        // find all matching records
        $records = $recordRepository->getPossibleRecordsBroken($score);

        foreach ($records as $record) {
            if ($record->getNumHolders() > 1) {
                // TODO implement in #74
                continue;
            }

            $holder = RecordManager::createHolder($record, [$score]);

            // is it better?
            if (!RecordManager::beatsRecord($record, $holder)) {
                continue;
            }

            // yes: add new UNCONFIRMED record
            foreach ($holder->getPeople() as $person) {
                $em->persist($person);
            }
            $em->persist($holder);
        }

        $em->flush();
    }
}