<?php

namespace AppBundle\View\Model;

use AppBundle\Constants;
use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Services\Records\RecordManager;

class RecordViewModel
{
    /**
     * @var Record
     */
    private $record;

    /**
     * @var string
     */
    private $roundName;
    /**
     * @var bool
     */
    private $unclaimed;
    /**
     * @var int
     */
    private $score;
    /**
     * @var RecordPersonViewModel[]
     */
    private $holders;
    /**
     * @var string
     */
    private $details;

    /**
     * @param Record            $record
     * @param RecordHolder|null $current_holder
     */
    public function __construct(Record $record, RecordHolder $current_holder = null)
    {
        $this->record = $record;

        $this->roundName = RecordManager::getRoundName($record);
        $this->unclaimed = $current_holder === null;

        if ($current_holder === null) {
            return;
        }

        $this->score = $current_holder->getScore();
        $this->holders = $current_holder->getPeople()->map(function (RecordHolderPerson $person) {
            return new RecordPersonViewModel($person->getPerson(), $person->getScoreValue());
        });

        $this->details = $current_holder->getCompetition()->getName() . ', ' . $current_holder->getDate()->format(Constants::DATE_FORMAT);
    }

    /**
     * @return Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->record->getSortOrder();
    }

    /**
     * @return string
     */
    public function getRoundName()
    {
        return $this->roundName;
    }

    /**
     * @return bool
     */
    public function isUnclaimed()
    {
        return $this->unclaimed;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return RecordPersonViewModel[]
     */
    public function getHolders()
    {
        return $this->holders;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }
}
