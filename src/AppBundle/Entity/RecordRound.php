<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_round")
 */
class RecordRound
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="rounds")
     */
    protected $record;

    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;
    /**
     * @ORM\Column(type="integer")
     */
    protected $count;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $skill;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bowtype;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender;

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
     * Set record
     *
     * @param Record $record
     *
     * @return RecordRound
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
     * Set round
     *
     * @param Round $round
     *
     * @return RecordRound
     */
    public function setRound(Round $round = null)
    {
        $this->round = $round;
    
        return $this;
    }

    /**
     * Get round
     *
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return RecordRound
     */
    public function setCount($count)
    {
        $this->count = $count;
    
        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set skill
     *
     * @param string $skill
     *
     * @return RecordRound
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;
    
        return $this;
    }

    /**
     * Get skill
     *
     * @return string
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set bowtype
     *
     * @param string $bowtype
     *
     * @return RecordRound
     */
    public function setBowtype($bowtype)
    {
        $this->bowtype = $bowtype;
    
        return $this;
    }

    /**
     * Get bowtype
     *
     * @return string
     */
    public function getBowtype()
    {
        return $this->bowtype;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return RecordRound
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
}
