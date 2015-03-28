<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competition")
 */
class Competition
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $info_only;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $boss_count;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $target_count = 4;

    /**
     * @ORM\OneToMany(targetEntity="CompetitionEntry", mappedBy="competition")
     */
    protected $entries;

    /**
     * @ORM\ManyToMany(targetEntity="Round")
     * @ORM\JoinTable(name="competition_round",
     *      joinColumns={@ORM\JoinColumn(name="competition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="round_id", referencedColumnName="id")}
     *      )
     */
    protected $rounds;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rounds = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Competition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Competition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set info_only
     *
     * @param boolean $infoOnly
     *
     * @return Competition
     */
    public function setInfoOnly($infoOnly)
    {
        $this->info_only = $infoOnly;

        return $this;
    }

    /**
     * Get info_only
     *
     * @return boolean
     */
    public function getInfoOnly()
    {
        return $this->info_only;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Competition
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
     *
     * @return Competition
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
     * Set boss_count
     *
     * @param integer $bossCount
     *
     * @return Competition
     */
    public function setBossCount($bossCount)
    {
        $this->boss_count = $bossCount;

        return $this;
    }

    /**
     * Get boss_count
     *
     * @return integer
     */
    public function getBossCount()
    {
        return $this->boss_count;
    }

    /**
     * Set target_count
     *
     * @param integer $targetCount
     *
     * @return Competition
     */
    public function setTargetCount($targetCount)
    {
        $this->target_count = $targetCount;

        return $this;
    }

    /**
     * Get target_count
     *
     * @return integer
     */
    public function getTargetCount()
    {
        return $this->target_count;
    }

    /**
     * Add entries
     *
     * @param \AppBundle\Entity\CompetitionEntry $entries
     *
     * @return Competition
     */
    public function addEntry(\AppBundle\Entity\CompetitionEntry $entries)
    {
        $this->entries[] = $entries;

        return $this;
    }

    /**
     * Remove entries
     *
     * @param \AppBundle\Entity\CompetitionEntry $entries
     */
    public function removeEntry(\AppBundle\Entity\CompetitionEntry $entries)
    {
        $this->entries->removeElement($entries);
    }

    /**
     * Get entries
     *
     * @return \Doctrine\Common\Collections\Collection|CompetitionEntry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Add rounds
     *
     * @param \AppBundle\Entity\Round $rounds
     *
     * @return Competition
     */
    public function addRound(\AppBundle\Entity\Round $rounds)
    {
        $this->rounds[] = $rounds;

        return $this;
    }

    /**
     * Remove rounds
     *
     * @param \AppBundle\Entity\Round $rounds
     */
    public function removeRound(\AppBundle\Entity\Round $rounds)
    {
        $this->rounds->removeElement($rounds);
    }

    /**
     * Get rounds
     *
     * @return \Doctrine\Common\Collections\Collection|Round[]
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @return int
     */
    public function getTotalSpaces()
    {
        return $this->getTargetCount() * $this->getBossCount();
    }

    /**
     * @return int
     */
    public function getFreeSpaces()
    {
        return $this->getTotalSpaces() - $this->getEntries()->count();
    }

    public function hasEntered(Person $user)
    {
        return $this->getEntries()->exists(function($_, CompetitionEntry $entry) use ($user) {
            $person = $entry->getPerson();
            return $person && $person->getId() == $user->getId();
        });
    }
}
