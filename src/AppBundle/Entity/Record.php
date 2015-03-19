<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="record")
 */
class Record {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * TODO inverse?
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;
    /**
     * @ORM\Column(type="integer")
     */
    protected $num_holders = 1;
    /**
     * @ORM\Column(type="string")
     */
    protected $skill;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender;

    /**
     * @ORM\OneToMany(targetEntity="RecordHolder", mappedBy="record")
     */
    protected $holders;

    public function __constructor() {
        $this->holders = new ArrayCollection();
    }

    /**
     * @return AppBundle\Entity\RecordHolder[]
     */
    public function getCurrentHolders() {
        //TODO
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->holders = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set num_holders
     *
     * @param integer $numHolders
     * @return Record
     */
    public function setNumHolders($numHolders)
    {
        $this->num_holders = $numHolders;

        return $this;
    }

    /**
     * Get num_holders
     *
     * @return integer 
     */
    public function getNumHolders()
    {
        return $this->num_holders;
    }

    /**
     * Set skill
     *
     * @param string $skill
     * @return Record
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
     * Set gender
     *
     * @param string $gender
     * @return Record
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

    /**
     * Set round
     *
     * @param \AppBundle\Entity\Round $round
     * @return Record
     */
    public function setRound(\AppBundle\Entity\Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return \AppBundle\Entity\Round 
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Add holders
     *
     * @param \AppBundle\Entity\RecordHolder $holders
     * @return Record
     */
    public function addHolder(\AppBundle\Entity\RecordHolder $holders)
    {
        $this->holders[] = $holders;

        return $this;
    }

    /**
     * Remove holders
     *
     * @param \AppBundle\Entity\RecordHolder $holders
     */
    public function removeHolder(\AppBundle\Entity\RecordHolder $holders)
    {
        $this->holders->removeElement($holders);
    }

    /**
     * Get holders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHolders()
    {
        return $this->holders;
    }
}
