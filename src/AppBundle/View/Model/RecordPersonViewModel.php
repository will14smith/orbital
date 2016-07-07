<?php

namespace AppBundle\View\Model;

use AppBundle\Entity\Person;

class RecordPersonViewModel
{
    /**
     * @var Person
     */
    private $person;
    /**
     * @var int
     */
    private $score;

    public function __construct(Person $person, int $score)
    {
        $this->person = $person;
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->person->getName();
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }
}
