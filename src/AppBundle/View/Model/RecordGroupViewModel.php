<?php

namespace AppBundle\View\Model;

class RecordGroupViewModel
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var RecordSubgroupViewModel[]
     */
    private $subgroups;

    /**
     * @param string                    $name
     * @param RecordSubgroupViewModel[] $subgroups
     */
    public function __construct(string $name, array $subgroups)
    {
        $this->name = $name;
        $this->subgroups = $subgroups;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return RecordSubgroupViewModel[]
     */
    public function getSubgroups()
    {
        return $this->subgroups;
    }

    /**
     * @param int $offset
     *
     * @return RecordSubgroupViewModel
     */
    public function getSubgroup(int $offset)
    {
        return $this->subgroups[$offset];
    }
}
