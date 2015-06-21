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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hosted;

    /**
     * @ORM\OneToMany(targetEntity="CompetitionSession", mappedBy="competition")
     */
    protected $sessions;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set location
     *
     * @param string $location
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
     * Set hosted
     *
     * @param boolean $hosted
     * @return Competition
     */
    public function setHosted($hosted)
    {
        $this->hosted = $hosted;
    
        return $this;
    }

    /**
     * Get hosted
     *
     * @return boolean 
     */
    public function getHosted()
    {
        return $this->hosted;
    }

    /**
     * Add sessions
     *
     * @param \AppBundle\Entity\CompetitionSession $sessions
     * @return Competition
     */
    public function addSession(\AppBundle\Entity\CompetitionSession $sessions)
    {
        $this->sessions[] = $sessions;
    
        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \AppBundle\Entity\CompetitionSession $sessions
     */
    public function removeSession(\AppBundle\Entity\CompetitionSession $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection|CompetitionSession[]
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
