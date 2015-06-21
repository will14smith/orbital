<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competition_session")
 */
class CompetitionSession
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="sessions")
     */
    protected $competition;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startTime;

    /**
     * Number of bosses available
     *
     * @ORM\Column(type="integer")
     */
    protected $bossCount;
    /**
     * Number of targets per boss
     *
     * @ORM\Column(type="integer")
     */
    protected $targetCount;
    /**
     * Number of details
     * e.g. 2 details = AB shoot, then CD
     *
     * @ORM\Column(type="integer")
     */
    protected $detailCount;

    /**
     * @ORM\OneToMany(targetEntity="CompetitionSessionRound", mappedBy="session")
     */
    protected $rounds;

    /**
     * @ORM\OneToMany(targetEntity="CompetitionSessionEntry", mappedBy="session")
     */
    protected $entries;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rounds = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entries = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return CompetitionSession
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set bossCount
     *
     * @param integer $bossCount
     * @return CompetitionSession
     */
    public function setBossCount($bossCount)
    {
        $this->bossCount = $bossCount;
    
        return $this;
    }

    /**
     * Get bossCount
     *
     * @return integer 
     */
    public function getBossCount()
    {
        return $this->bossCount;
    }

    /**
     * Set targetCount
     *
     * @param integer $targetCount
     * @return CompetitionSession
     */
    public function setTargetCount($targetCount)
    {
        $this->targetCount = $targetCount;
    
        return $this;
    }

    /**
     * Get targetCount
     *
     * @return integer 
     */
    public function getTargetCount()
    {
        return $this->targetCount;
    }

    /**
     * Set detailCount
     *
     * @param integer $detailCount
     * @return CompetitionSession
     */
    public function setDetailCount($detailCount)
    {
        $this->detailCount = $detailCount;
    
        return $this;
    }

    /**
     * Get detailCount
     *
     * @return integer 
     */
    public function getDetailCount()
    {
        return $this->detailCount;
    }

    /**
     * Set competition
     *
     * @param \AppBundle\Entity\Competition $competition
     * @return CompetitionSession
     */
    public function setCompetition(\AppBundle\Entity\Competition $competition = null)
    {
        $this->competition = $competition;
    
        return $this;
    }

    /**
     * Get competition
     *
     * @return \AppBundle\Entity\Competition 
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Add rounds
     *
     * @param \AppBundle\Entity\CompetitionSessionRound $rounds
     * @return CompetitionSession
     */
    public function addRound(\AppBundle\Entity\CompetitionSessionRound $rounds)
    {
        $this->rounds[] = $rounds;
    
        return $this;
    }

    /**
     * Remove rounds
     *
     * @param \AppBundle\Entity\CompetitionSessionRound $rounds
     */
    public function removeRound(\AppBundle\Entity\CompetitionSessionRound $rounds)
    {
        $this->rounds->removeElement($rounds);
    }

    /**
     * Get rounds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Add entries
     *
     * @param \AppBundle\Entity\CompetitionSessionEntry $entries
     * @return CompetitionSession
     */
    public function addEntry(\AppBundle\Entity\CompetitionSessionEntry $entries)
    {
        $this->entries[] = $entries;
    
        return $this;
    }

    /**
     * Remove entries
     *
     * @param \AppBundle\Entity\CompetitionSessionEntry $entries
     */
    public function removeEntry(\AppBundle\Entity\CompetitionSessionEntry $entries)
    {
        $this->entries->removeElement($entries);
    }

    /**
     * Get entries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
