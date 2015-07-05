<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competition_session_round")
 */
class CompetitionSessionRound
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CompetitionSession", inversedBy="rounds")
     */
    protected $session;
    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;

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
     * Set session
     *
     * @param \AppBundle\Entity\CompetitionSession $session
     * @return CompetitionSessionRound
     */
    public function setSession(\AppBundle\Entity\CompetitionSession $session = null)
    {
        $this->session = $session;
    
        return $this;
    }

    /**
     * Get session
     *
     * @return \AppBundle\Entity\CompetitionSession 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set round
     *
     * @param \AppBundle\Entity\Round $round
     * @return CompetitionSessionRound
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
}
