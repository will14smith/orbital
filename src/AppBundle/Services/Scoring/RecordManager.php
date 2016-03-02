<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;

class RecordManager
{
    public static function beatsRecord(Record $record, RecordHolder $holder)
    {
        $current_holder = $record->getCurrentHolder();
        if (!$current_holder) {
            return true;
        }

        return $current_holder->getScore() < $holder->getScore();
    }

    public static function approveHolder(Record $record, RecordHolder $holder)
    {
        // check it breaks the record
        if(!self::beatsRecord($record, $holder)) {
            throw new \Exception('New holder ' . $holder->getId() . ' doesn\'t break the record.');

        }

        // update current holder to have a broken date
        $current_holder = $record->getCurrentHolder();
        if ($current_holder) {
            $current_holder->setDateBroken($holder->getDate());
        }

        // mark holder as confirmed
        $holder->setDateConfirmed(new \DateTime());
    }

    /**
     * @param Record $record
     * @param Score[] $scores
     * @return RecordHolder
     */
    public static function createHolder($record, array $scores)
    {
        $holder = new RecordHolder();

        $holder->setRecord($record);
        $holder->setLocation('?');
        $holder->setDate($scores[0]->getDateShot());

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
     *
     * @return RecordHolder
     * @throws \Exception
     */
    public static function getCurrentHolder(Record $record)
    {
        $filtered = self::getConfirmedHolders($record)->filter(function (RecordHolder $holder) {
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
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolder[]
     */
    public static function getConfirmedHolders(Record $record)
    {
        return $record->getAllHolders()->filter(function (RecordHolder $holder) {
            return $holder->getDateConfirmed() !== null;
        });
    }

    /**
     * @param Record $record
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolder[]
     */
    public static function getUnconfirmedHolders(Record $record)
    {
        return $record->getAllHolders()->filter(function (RecordHolder $holder) {
            return $holder->getDateConfirmed() === null;
        });
    }

    public static function toString($record)
    {
        // TODO rewrite
        throw new \Exception("TODO");

        $name = Skill::display($record->skill);

        if ($record->gender) {
            $name .= ' ' . Gender::display($record->gender);
        }

        if ($record->bowtype) {
            $name .= ' ' . BowType::display($record->bowtype);
        }

        $name .= ' ' . $record->getRound()->getName();

        if ($record->num_holders > 1) {
            $name .= ' Team';
        }

        return $name;
    }
}