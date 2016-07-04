<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PersonHandicapRepository")
 * @ORM\Table(name="person_handicap")
 */
class PersonHandicap
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person",inversedBy="handicaps")
     */
    protected $person;
    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $type;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $handicap;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $indoor;

    /**
     * @ORM\ManyToOne(targetEntity="Score")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $score;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return PersonHandicap
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return PersonHandicap
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set handicap.
     *
     * @param int $handicap
     *
     * @return PersonHandicap
     */
    public function setHandicap($handicap)
    {
        $this->handicap = $handicap;

        return $this;
    }

    /**
     * Get handicap.
     *
     * @return int
     */
    public function getHandicap()
    {
        return $this->handicap;
    }

    /**
     * Set person.
     *
     * @param \AppBundle\Entity\Person $person
     *
     * @return PersonHandicap
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set score.
     *
     * @param \AppBundle\Entity\Score $score
     *
     * @return PersonHandicap
     */
    public function setScore(Score $score = null)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score.
     *
     * @return \AppBundle\Entity\Score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set indoor.
     *
     * @param bool $indoor
     *
     * @return Round
     */
    public function setIndoor($indoor)
    {
        $this->indoor = $indoor;

        return $this;
    }

    /**
     * Get indoor.
     *
     * @return bool
     */
    public function getIndoor()
    {
        return $this->indoor;
    }
}
