<?php

namespace AppBundle\View\Model;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;

class RecordSubgroupViewModel
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $team;
    /**
     * @var RecordViewModel[]
     */
    private $records;

    /**
     * @param string $name
     * @param bool   $team
     */
    public function __construct(string $name, bool $team)
    {
        $this->name = $name;
        $this->team = $team;
        $this->records = [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isTeam()
    {
        return $this->team;
    }

    /**
     * @return RecordViewModel[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record $record
     */
    public function addUnclaimed(Record $record)
    {
        $this->records[] = new RecordViewModel($record, null);
    }

    /**
     * @param Record       $record
     * @param RecordHolder $currentHolder
     */
    public function addRecord(Record $record, RecordHolder $currentHolder)
    {
        $this->records[] = new RecordViewModel($record, $currentHolder);
    }

    /**
     * @return RecordSubgroupViewModel
     */
    public function sort()
    {
        $new_subgroup = new self($this->name, $this->team);
        $new_subgroup->records = $this->records;

        usort($new_subgroup->records, function (RecordViewModel $a, RecordViewModel $b) {
            return $a->getRecord()->getSortOrder() - $b->getRecord()->getSortOrder();
        });

        return $new_subgroup;
    }
}
