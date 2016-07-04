<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Person;
use AppBundle\Services\Enum\BowType;

class HandicapIdentifier
{
    /** @var Person */
    private $person;
    /** @var bool */
    private $indoor;
    /** @var string */
    private $bowtype;

    public function __construct(Person $person, $indoor, $bowtype)
    {
        if (!is_bool($indoor)) {
            throw new \Exception('Must pass boolean for parameter "$indoor"');
        }
        if (!is_string($bowtype) && BowType::isValid($bowtype)) {
            throw new \Exception('Must pass boolean for parameter "$indoor"');
        }

        $this->person = $person;
        $this->indoor = $indoor;
        $this->bowtype = $bowtype;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function isIndoor()
    {
        return $this->indoor;
    }

    /**
     * @return string
     */
    public function getBowtype()
    {
        return $this->bowtype;
    }
}
