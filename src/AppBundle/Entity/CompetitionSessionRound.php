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
     * @param CompetitionSession $session
     * @return CompetitionSessionRound
     */
    public function setSession(CompetitionSession $session = null)
    {
        $this->session = $session;
    
        return $this;
    }

    /**
     * Get session
     *
     * @return CompetitionSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set round
     *
     * @param Round $round
     * @return CompetitionSessionRound
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
}
