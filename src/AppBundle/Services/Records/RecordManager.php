<?php

namespace AppBundle\Services\Records;

use AppBundle\Entity\Club;
use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\RecordRound;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;

class RecordManager
{
    public static function beatsRecord(Record $record, RecordHolder $holder)
    {
        $current_holder = $record->getCurrentHolder($holder->getClub());
        if (!$current_holder) {
            return true;
        }

        return $holder->isBetterThan($current_holder);
    }
    public static function consistentClub(RecordHolder $holder)
    {
        $holderClub = $holder->getClub();

        foreach($holder->getPeople() as $personHolder) {
            $score = $personHolder->getScore();

            if(!$score) {
                continue;
            }

            if($score->getClub()->getId() != $holderClub->getId()) {
                return false;
            }
        }

        return true;
    }

    public static function approveHolder(Record $record, RecordHolder $holder)
    {
        // check it breaks the record
        if (!self::beatsRecord($record, $holder)) {
            throw new \Exception('New holder ' . $holder->getId() . ' doesn\'t break the record.');
        }
        if (!self::consistentClub($holder)) {
            throw new \Exception('New holder ' . $holder->getId() . ' isn\'t all same club.');
        }

        // update current holder to have a broken date
        $current_holder = $record->getCurrentHolder($holder->getClub());
        if ($current_holder) {
            $current_holder->setDateBroken($holder->getDate());
        }

        // mark holder as confirmed
        $holder->setDateConfirmed(new \DateTime());
    }

    /**
     * @param Record $record
     * @param Score[] $scores
     *
     * @return RecordHolder
     *
     * @throws \Exception
     */
    public static function createHolder($record, array $scores)
    {
        $competition = $scores[0]->getCompetition();
        foreach ($scores as $score) {
            if ($score->getCompetition()->getId() != $competition->getId()) {
                throw new \Exception('Scores not at same competition');
            }
        }

        $holder = new RecordHolder();

        $holder->setRecord($record);
        $holder->setCompetition($competition);
        $holder->setDate($scores[0]->getDateShot());
        $holder->setClub($scores[0]->getClub());

        foreach ($scores as $score) {
            $holder->addPerson(self::createHolderPerson($score));
        }

        self::syncHolder($holder);

        return $holder;
    }

    /**
     * @param RecordHolder $holder
     */
    public static function syncHolder(RecordHolder $holder)
    {
        $total = 0;
        foreach ($holder->getPeople() as $person) {
            $person->setRecordHolder($holder);

            $total += $person->getScoreValue();
        }

        $holder->setScore($total);
    }

    /**
     * @param Score $score
     * @return RecordHolderPerson
     */
    public static function createHolderPerson(Score $score)
    {
        $person = new RecordHolderPerson();

        $person->setPerson($score->getPerson());
        $person->setScore($score);
        $person->setScoreValue($score->getScore());

        return $person;
    }

    /**
     * @param Record $record
     * @param Club $club
     *
     * @return RecordHolder
     * @throws \Exception
     */
    public static function getCurrentHolder(Record $record, Club $club)
    {
        $filtered = self::getConfirmedHolders($record, $club)->filter(function (RecordHolder $holder) {
            return $holder->getDateBroken() === null;
        });

        $count = $filtered->count();

        if ($count == 0) {
            return null;
        } else if ($count == 1) {
            return $filtered->first();
        } else {
            throw new \Exception("Multiple current holders of " . $record->getId());
        }
    }

    /**
     * @param Record $record
     * @param Club $club
     *
     * @return \AppBundle\Entity\RecordHolder[]|\Doctrine\Common\Collections\Collection
     */
    public static function getConfirmedHolders(Record $record, Club $club)
    {
        return $record->getAllHolders($club)->filter(function (RecordHolder $holder) {
            return $holder->getDateConfirmed() !== null;
        });
    }

    /**
     * @param Record $record
     * @param Club $club
     *
     * @return \AppBundle\Entity\RecordHolder[]|\Doctrine\Common\Collections\Collection
     */
    public static function getUnconfirmedHolders(Record $record, Club $club)
    {
        return $record->getAllHolders($club)->filter(function (RecordHolder $holder) {
            return $holder->getDateConfirmed() === null;
        });
    }

    public static function toString(Record $record)
    {
        $rounds = $record->getRounds();
        $roundName = self::getRoundName($record);

        $allNovices = $record->isNovice();

        $primaryGender = $rounds[0]->getGender();
        $allGender = $rounds->forAll(function ($_, RecordRound $round) use ($primaryGender) {
            return $round->getGender() == $primaryGender;
        });

        $name = Skill::display($allNovices ? Skill::NOVICE : Skill::SENIOR);
        if ($record->getNumHolders() > 1) {
            if ($allGender && $primaryGender) {
                $name .= ' ' . Gender::display($primaryGender);
            }

            $name .= ' Team - ';

            $name .= $roundName;
        } else {
            if ($rounds->count() > 1) {
                throw new \Exception('Unexpected record configuration, indv records shouldn\'t be multi-rounds');
            }

            /** @var RecordRound $round */
            $round = $rounds[0];
            if ($round->getGender()) {
                $name .= ' ' . Gender::display($round->getGender());
            }
            if ($round->getBowtype()) {
                $name .= ' ' . BowType::display($round->getBowtype());
            }

            if (!$round->getGender() || !$round->getBowtype()) {
                $name .= ' Individual';
            }

            $name .= ' - ' . $roundName;
        }

        return $name;
    }

    public static function getRoundName(Record $record)
    {
        $rounds = $record->getRounds();
        $roundNames = $rounds->map(function (RecordRound $round) {
            $roundName = $round->getRound();
            if ($round->getCount() == 1) {
                return $roundName;
            }
            if ($round->getCount() == 2) {
                return 'Double ' . $roundName;
            }

            return $round->getCount() . ' x ' . $roundName;
        });

        return join(' / ', array_unique($roundNames->toArray()));
    }
}