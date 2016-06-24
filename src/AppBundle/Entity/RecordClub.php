<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_club")
 */
class RecordClub
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="clubs")
     */
    protected $record;
    /**
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $activeFrom;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $activeUntil;

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
     * Set activeFrom
     *
     * @param \DateTime $activeFrom
     *
     * @return RecordClub
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = $activeFrom;
    
        return $this;
    }

    /**
     * Get activeFrom
     *
     * @return \DateTime
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Set activeUntil
     *
     * @param \DateTime $activeUntil
     *
     * @return RecordClub
     */
    public function setActiveUntil($activeUntil)
    {
        $this->activeUntil = $activeUntil;
    
        return $this;
    }

    /**
     * Get activeUntil
     *
     * @return \DateTime
     */
    public function getActiveUntil()
    {
        return $this->activeUntil;
    }

    /**
     * Set record
     *
     * @param Record $record
     *
     * @return RecordClub
     */
    public function setRecord(Record $record = null)
    {
        $this->record = $record;
    
        return $this;
    }

    /**
     * Get record
     *
     * @return Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set club
     *
     * @param Club $club
     *
     * @return RecordClub
     */
    public function setClub(Club $club = null)
    {
        $this->club = $club;
    
        return $this;
    }

    /**
     * Get club
     *
     * @return Club
     */
    public function getClub()
    {
        return $this->club;
    }

    public function isActive(\DateTime $date = null) {
        if($date === null) {
            $date = new \DateTime('now');
        }

        return $date >= $this->getActiveFrom() && $date < $this->getActiveUntil();
    }
}
