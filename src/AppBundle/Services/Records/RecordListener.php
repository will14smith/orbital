<?php

namespace AppBundle\Services\Records;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordRepository;
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

    /**
     * @param ScoreEvent $event
     * @return RecordHolder[]
     */
    public function handleScore(ScoreEvent $event)
    {
        $score = $event->getScore();

        if (!$score->getDateAccepted()) {
            return [];
        }
        if (!$score->getCompetition()) {
            return [];
        }

        return $this->checkForRecord($score);
    }

    private function checkForRecord(Score $score)
    {
        $em = $this->doctrine->getManager();

        /** @var RecordRepository $recordRepository */
        $recordRepository = $em->getRepository('AppBundle:Record');

        // find all matching records
        $records = $recordRepository->getPossibleRecordsBroken($score);

        $newHolders = [];

        foreach ($records as $record) {
            $scores = $this->getCandidateScores($em, $record, $score);
            $holder = $this->buildRecord($record, $scores);
            if($holder != null) {
                $newHolders = array_merge($newHolders, $this->addNewRecordIfBetter($em, $record, $holder));
            }
        }

        $em->flush();

        return $newHolders;
    }

    /**
     * @param ObjectManager $em
     * @param Record $record
     * @param Score $score
     *
     * @return Score[]
     */
    private function getCandidateScores(ObjectManager $em, Record $record, Score $score)
    {
        $competition = $score->getCompetition();

        $scoreRepository = $em->getRepository('AppBundle:Score');

        if($record->getNumHolders() == 1) {
            $person = $score->getPerson();

            return $scoreRepository->getByCompetitionAndPerson($competition, $person, $score->getClub());
        } else {
            return $scoreRepository->getByCompetition($competition, $score->getClub());
        }
    }

    private function addNewRecordIfBetter(ObjectManager $em, Record $record, RecordHolder $holder)
    {
        if (!RecordManager::beatsRecord($record, $holder)) {
            return [];
        }

        foreach($record->getUnconfirmedHolders($holder->getClub()) as $unconfirmedHolder) {
            if($unconfirmedHolder->getCompetition()->getId() !== $holder->getCompetition()->getId()) {
                continue;
            }

            if(!$holder->isBetterThan($unconfirmedHolder)) {
                return [];
            }

            $em->remove($unconfirmedHolder);

            break;
        }

        $em->persist($holder);

        return [$holder];
    }


    /**
     * @param Record $record
     * @param Score[] $candidateScores
     *
     * @return RecordHolder
     */
    public function buildRecord(Record $record, array $candidateScores)
    {
        if ($record->getNumHolders() == 1) {
            return $this->buildIndividualRecord($record, $candidateScores);
        } else {
            return $this->buildTeamRecord($record, $candidateScores);
        }
    }

    /**
     * PRE: $scores all same competition
     * PRE: $scores all same person
     * PRE: $scores all same club
     * PRE: $scores is sorted by date
     *
     * @param Record $record
     * @param Score[] $candidateScores
     *
     * @return RecordHolder
     *
     * @throws \Exception
     */
    public function buildIndividualRecord(Record $record, array $candidateScores)
    {
        $rounds = $record->getRounds();
        if ($rounds->count() != 1) {
            throw new \Exception('Individual records should have a single round.');
        }

        $round = $rounds[0];

        // filter scores
        $candidateScores = array_filter($candidateScores, function ($s) use($record) { return $this->canBeUsedInRecord($record, $s); });

        // NOTE: only the first N scores count for records
        // NOTE: single scores can count for double records (but are unlikely to win)
        $selectedScores = array_slice($candidateScores, 0, $round->getCount());

        if(count($selectedScores) == 0) {
            return null;
        }

        return RecordManager::createHolder($record, $selectedScores);
    }

    /**
     * PRE: $scores all same competition
     * PRE: $scores all same club
     * PRE: $scores is sorted by date
     *
     * @param Record $record
     * @param Score[] $candidateScores
     *
     * @return RecordHolder
     *
     * @throws \Exception
     */
    public function buildTeamRecord(Record $record, array $candidateScores)
    {
        $rounds = $record->getRounds();
        foreach ($rounds as $round) {
            if ($round->getCount() != 1) {
                throw new \Exception('Team records shouldn\'t have multi-rounds');
            }
        }

        // find highest scores without repeating people
        $maxCount = $record->getNumHolders();
        $selectedCount = 0;
        /** @var Score[] $selectedScores */
        $selectedScores = [];

        foreach ($candidateScores as $candidateScore) {
            // can the score can be used for this record
            if(!$this->canBeUsedInRecord($record, $candidateScore)) {
                continue;
            }

            // if score.person has selected score already: remove existing if this is better
            for ($i = 0; $i < $selectedCount; $i++) {
                $score = $selectedScores[$i];

                if ($score->getPerson()->getId() != $candidateScore->getPerson()->getId()) {
                    continue;
                }

                if (!$candidateScore->isBetterThan($score)) {
                    continue;
                }

                // remove candidate
                array_splice($selectedScores, $i, 1);
                $selectedCount--;

                break;
            }

            // selected list is full
            if ($selectedCount == $maxCount) {
                // if score < lowest candidate: continue
                if ($selectedCount > 0 && !$candidateScore->isBetterThan($selectedScores[$selectedCount - 1])) {
                    continue;
                }

                // remove lowest
                array_splice($selectedScores, $selectedCount - 1, 1);
                $selectedCount--;
            }

            // insertion sort!
            for ($i = 0; $i < $selectedCount; $i++) {
                $score = $selectedScores[$i];

                if (!$candidateScore->isBetterThan($score)) {
                    continue;
                }

                array_splice($selectedScores, $i, 0, [$candidateScore]);
                $selectedCount++;

                continue 2;
            }

            array_push($selectedScores, $candidateScore);
            $selectedCount++;
        }

        if(count($selectedScores) == 0) {
            return null;
        }

        return RecordManager::createHolder($record, $selectedScores);
    }

    /**
     * @param Record $record
     * @param Score $score
     *
     * @return bool
     */
    private function canBeUsedInRecord(Record $record, Score $score) {
        if(!$score->getCompetition()) {
            return false;
        }

        $round = $score->getRound();

        foreach($record->getRounds() as $recordRound) {
            if($recordRound->getRound()->getId() !== $round->getId()) {
                continue;
            }


            $skill = $recordRound->getSkill();
            if($skill && $skill !== $score->getSkill()) {
                continue;
            }

            $gender = $recordRound->getGender();
            if($gender && $gender !== $score->getPerson()->getGender()) {
                continue;
            }

            $bowtype = $recordRound->getBowtype();
            if($bowtype && $bowtype !== $score->getBowtype()) {
                continue;
            }

            // found an allowed round
            return true;
        }

        return false;
    }
}