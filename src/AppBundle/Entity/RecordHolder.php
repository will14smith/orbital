<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_holder")
 */
class RecordHolder
{
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
     * @ORM\Column(type="integer")
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_broken;

    /**
     * @ORM\OneToMany(targetEntity="RecordHolderPerson", mappedBy="record_holder")
     */
    protected $people;

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
     * Constructor
     */
    public function __construct()
    {
        $this->people = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return RecordHolder
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Add people
     *
     * @param \AppBundle\Entity\RecordHolderPerson $people
     * @return RecordHolder
     */
    public function addPerson(\AppBundle\Entity\RecordHolderPerson $people)
    {
        $this->people[] = $people;

        return $this;
    }

    /**
     * Remove people
     *
     * @param \AppBundle\Entity\RecordHolderPerson $people
     */
    public function removePerson(\AppBundle\Entity\RecordHolderPerson $people)
    {
        $this->people->removeElement($people);
    }

    /**
     * Get people
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolderPerson[]
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * Set date_broken
     *
     * @param \DateTime $dateBroken
     * @return RecordHolder
     */
    public function setDateBroken($dateBroken)
    {
        $this->date_broken = $dateBroken;

        return $this;
    }

    /**
     * Get date_broken
     *
     * @return \DateTime 
     */
    public function getDateBroken()
    {
        return $this->date_broken;
    }
}
