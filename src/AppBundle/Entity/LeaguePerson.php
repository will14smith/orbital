<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="league_person")
 */
class LeaguePerson
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="people")
     */
    protected $league;
    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_added;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $initial_position;
    /**
     * @ORM\Column(type="integer")
     */
    protected $points;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date_added
     *
     * @param \DateTime $dateAdded
     * @return LeaguePerson
     */
    public function setDateAdded($dateAdded)
    {
        $this->date_added = $dateAdded;

        return $this;
    }

    /**
     * Get date_added
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->date_added;
    }

    /**
     * Set initial_position
     *
     * @param integer $initialPosition
     * @return LeaguePerson
     */
    public function setInitialPosition($initialPosition)
    {
        $this->initial_position = $initialPosition;

        return $this;
    }

    /**
     * Get initial_position
     *
     * @return integer 
     */
    public function getInitialPosition()
    {
        return $this->initial_position;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return LeaguePerson
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set league
     *
     * @param \AppBundle\Entity\League $league
     * @return LeaguePerson
     */
    public function setLeague(\AppBundle\Entity\League $league = null)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league
     *
     * @return \AppBundle\Entity\League 
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return LeaguePerson
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \AppBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }
}
