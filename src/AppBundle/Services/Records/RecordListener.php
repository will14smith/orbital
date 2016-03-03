<?php

namespace AppBundle\Services\Records;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\Score;
use AppBundle\Services\Events\ScoreEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;

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

        if (!$score->getDateAccepted()) {
            return;
        }

        if ($score->getCompetition()) {
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

            if ($record->getNumHolders() == 1) {
                $this->checkForInvidualRecord($em, $score, $record);
            } else {
                $this->checkForTeamRecord($em, $score, $record);
            }
        }

        $em->flush();
    }

    private function checkForInvidualRecord(ObjectManager $em, Score $score, Record $record)
    {
        $rounds = $record->getRounds();

        if ($rounds->count() != 1) {
            throw new \Exception('Individual records should have a single round.');
        }

        $round = $rounds[0];
        if ($round->getCount() > 1) {
            throw new \Exception('TODO - implement n-round records (i.e. double portsmouth).');
        } else {
            $holder = RecordManager::createHolder($record, [$score]);
        }

        $this->addNewRecordIfBetter($em, $record, $holder);
    }

    private function checkForTeamRecord(ObjectManager $em, Score $score, Record $record)
    {
        throw new \Exception('TODO - implement team records.');
    }

    private function addNewRecordIfBetter(ObjectManager $em, Record $record, RecordHolder $holder)
    {
        if (!RecordManager::beatsRecord($record, $holder)) {
            return;
        }

        foreach ($holder->getPeople() as $person) {
            $em->persist($person);
        }
        $em->persist($holder);
    }
}