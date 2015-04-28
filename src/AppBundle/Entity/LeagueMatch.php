<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="league_match")
 */
class LeagueMatch
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="matches")
     */
    protected $league;

    /**
     * @ORM\ManyToOne(targetEntity="LeaguePerson")
     */
    protected $challenger;
    /**
     * @ORM\ManyToOne(targetEntity="LeaguePerson")
     */
    protected $challengee;

    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;

    /**
     * @ORM\ManyToOne(targetEntity="Score")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $challenger_score;
    /**
     * @ORM\ManyToOne(targetEntity="Score")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $challengee_score;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $challenger_score_value;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $challengee_score_value;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $accepted;
    /**
     * True = challenger won
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $result;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_challenged;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_confirmed;

    /**
     * @ORM\OneToMany(targetEntity="LeagueMatchProof", mappedBy="match")
     */
    protected $proofs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proofs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set challenger_score_value
     *
     * @param integer $challengerScoreValue
     *
     * @return LeagueMatch
     */
    public function setChallengerScoreValue($challengerScoreValue)
    {
        $this->challenger_score_value = $challengerScoreValue;

        return $this;
    }

    /**
     * Get challenger_score_value
     *
     * @return integer
     */
    public function getChallengerScoreValue()
    {
        return $this->challenger_score_value;
    }

    /**
     * Set challengee_score_value
     *
     * @param integer $challengeeScoreValue
     *
     * @return LeagueMatch
     */
    public function setChallengeeScoreValue($challengeeScoreValue)
    {
        $this->challengee_score_value = $challengeeScoreValue;

        return $this;
    }

    /**
     * Get challengee_score_value
     *
     * @return integer
     */
    public function getChallengeeScoreValue()
    {
        return $this->challengee_score_value;
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     *
     * @return LeagueMatch
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set result
     *
     * @param boolean $result
     *
     * @return LeagueMatch
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set date_challenged
     *
     * @param \DateTime $dateChallenged
     *
     * @return LeagueMatch
     */
    public function setDateChallenged($dateChallenged)
    {
        $this->date_challenged = $dateChallenged;

        return $this;
    }

    /**
     * Get date_challenged
     *
     * @return \DateTime
     */
    public function getDateChallenged()
    {
        return $this->date_challenged;
    }

    /**
     * Set date_confirmed
     *
     * @param \DateTime $dateConfirmed
     *
     * @return LeagueMatch
     */
    public function setDateConfirmed($dateConfirmed)
    {
        $this->date_confirmed = $dateConfirmed;

        return $this;
    }

    /**
     * Get date_confirmed
     *
     * @return \DateTime
     */
    public function getDateConfirmed()
    {
        return $this->date_confirmed;
    }

    /**
     * Set league
     *
     * @param \AppBundle\Entity\League $league
     *
     * @return LeagueMatch
     */
    public function setLeague(\AppBundle\Entity\League $league = null)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league
     *
     * @return \AppBundle\Entity\League
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set challenger
     *
     * @param \AppBundle\Entity\LeaguePerson $challenger
     *
     * @return LeagueMatch
     */
    public function setChallenger(\AppBundle\Entity\LeaguePerson $challenger = null)
    {
        $this->challenger = $challenger;

        return $this;
    }

    /**
     * Get challenger
     *
     * @return \AppBundle\Entity\LeaguePerson
     */
    public function getChallenger()
    {
        return $this->challenger;
    }

    /**
     * Set challengee
     *
     * @param \AppBundle\Entity\LeaguePerson $challengee
     *
     * @return LeagueMatch
     */
    public function setChallengee(\AppBundle\Entity\LeaguePerson $challengee = null)
    {
        $this->challengee = $challengee;

        return $this;
    }

    /**
     * Get challengee
     *
     * @return \AppBundle\Entity\LeaguePerson
     */
    public function getChallengee()
    {
        return $this->challengee;
    }

    /**
     * Get the person who won
     *
     * @return LeaguePerson
     */
    public function getWinner()
    {
        if (!$this->getAccepted()) {
            return false;
        }

        return $this->getResult() ? $this->getChallenger() : $this->getChallengee();
    }

    /**
     * Get the person who lost
     *
     * @return LeaguePerson
     */
    public function getLoser()
    {
        if (!$this->getAccepted()) {
            return false;
        }

        return !$this->getResult() ? $this->getChallenger() : $this->getChallengee();
    }

    /*
     * @return bool false doesn't mean no...
     */
    public function challengerWon() {
        if (!$this->getAccepted()) {
            return false;
        }

        return $this->getResult();
    }
    /*
     * @return bool false doesn't mean no...
     */
    public function challengeeWon() {
        if (!$this->getAccepted()) {
            return false;
        }

        return !$this->getResult();
    }

    /**
     * Set round
     *
     * @param \AppBundle\Entity\Round $round
     *
     * @return LeagueMatch
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
     * Set challenger_score
     *
     * @param \AppBundle\Entity\Score $challengerScore
     *
     * @return LeagueMatch
     */
    public function setChallengerScore(\AppBundle\Entity\Score $challengerScore = null)
    {
        $this->challenger_score = $challengerScore;

        return $this;
    }

    /**
     * Get challenger_score
     *
     * @return \AppBundle\Entity\Score
     */
    public function getChallengerScore()
    {
        return $this->challenger_score;
    }

    /**
     * Set challengee_score
     *
     * @param \AppBundle\Entity\Score $challengeeScore
     *
     * @return LeagueMatch
     */
    public function setChallengeeScore(\AppBundle\Entity\Score $challengeeScore = null)
    {
        $this->challengee_score = $challengeeScore;

        return $this;
    }

    /**
     * Get challengee_score
     *
     * @return \AppBundle\Entity\Score
     */
    public function getChallengeeScore()
    {
        return $this->challengee_score;
    }

    /**
     * Add proofs
     *
     * @param \AppBundle\Entity\LeagueMatchProof $proofs
     *
     * @return LeagueMatch
     */
    public function addProof(\AppBundle\Entity\LeagueMatchProof $proofs)
    {
        $this->proofs[] = $proofs;

        return $this;
    }

    /**
     * Remove proofs
     *
     * @param \AppBundle\Entity\LeagueMatchProof $proofs
     */
    public function removeProof(\AppBundle\Entity\LeagueMatchProof $proofs)
    {
        $this->proofs->removeElement($proofs);
    }

    /**
     * Get proofs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProofs()
    {
        return $this->proofs;
    }
}
