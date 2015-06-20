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
        $recordRepository = $this->doctrine->getRepository('AppBundle:Record');

        // find all matching records
        $records = $recordRepository->getPossibleRecordsBroken($score);

        foreach ($records as $record) {
            if ($record->getNumHolders() > 1) {
                // TODO implement in #74
                continue;
            }

            // is it better?
            $currentHolder = $record->getCurrentHolder();

            if ($currentHolder && $currentHolder->getScore() >= $score->getScore()) {
                continue;
            }

            // yes: add new UNCONFIRMED record
            $newHolder = $this->createHolder([$score]);

            $recordRepository->award($record, $newHolder);
        }
    }

    /**
     * @param Score[] $scores
     * @return RecordHolder
     */
    private function createHolder(array $scores)
    {
        $newHolder = new RecordHolder();

        $newHolder->setLocation('?');
        $newHolder->setDate($scores[0]->getDateShot());

        foreach ($scores as $score) {
            $newHolder->addPerson($this->createHolderPerson($score));
        }

        return $newHolder;
    }

    /**
     * @param Score $score
     * @return RecordHolderPerson
     */
    private function createHolderPerson(Score $score)
    {
        $newHolderPerson = new RecordHolderPerson();

        $newHolderPerson->setPerson($score->getPerson());
        $newHolderPerson->setScore($score);
        $newHolderPerson->setScoreValue($score->getScore());

        return $newHolderPerson;
    }
}