<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_holder")
 */
class RecordHolder {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="holders")
     */
    protected $record;

    /**
     * TODO inverse
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;

    /**
     * @ORM\Column(type="integer")
     */
    protected $score_value;
    /**
     * TODO inverse?
     * @ORM\JoinColumn(nullable=true)
     * @ORM\ManyToOne(targetEntity="Score")
     */
    protected $score;

    /**
     * @ORM\Column(type="string")
     */
    protected $location;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

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
     * Set score_value
     *
     * @param integer $scoreValue
     * @return RecordHolder
     */
    public function setScoreValue($scoreValue)
    {
        $this->score_value = $scoreValue;

        return $this;
    }

    /**
     * Get score_value
     *
     * @return integer 
     */
    public function getScoreValue()
    {
        return $this->score_value;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return RecordHolder
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return RecordHolder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set record
     *
     * @param \AppBundle\Entity\Record $record
     * @return RecordHolder
     */
    public function setRecord(\AppBundle\Entity\Record $record = null)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Get record
     *
     * @return \AppBundle\Entity\Record 
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return RecordHolder
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

    /**
     * Set score
     *
     * @param \AppBundle\Entity\Score $score
     * @return RecordHolder
     */
    public function setScore(\AppBundle\Entity\Score $score = null)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return \AppBundle\Entity\Score 
     */
    public function getScore()
    {
        return $this->score;
    }
}
