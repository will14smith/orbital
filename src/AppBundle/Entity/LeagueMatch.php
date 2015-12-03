<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
        $this->proofs = new ArrayCollection();
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
     * @param League $league
     *
     * @return LeagueMatch
     */
    public function setLeague(League $league = null)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league
     *
     * @return League
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set challenger
     *
     * @param LeaguePerson $challenger
     *
     * @return LeagueMatch
     */
    public function setChallenger(LeaguePerson $challenger = null)
    {
        $this->challenger = $challenger;

        return $this;
    }

    /**
     * Get challenger
     *
     * @return LeaguePerson
     */
    public function getChallenger()
    {
        return $this->challenger;
    }

    /**
     * Set challengee
     *
     * @param LeaguePerson $challengee
     *
     * @return LeagueMatch
     */
    public function setChallengee(LeaguePerson $challengee = null)
    {
        $this->challengee = $challengee;

        return $this;
    }

    /**
     * Get challengee
     *
     * @return LeaguePerson
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
     * @param Round $round
     *
     * @return LeagueMatch
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
     * Set challenger_score
     *
     * @param Score $challengerScore
     *
     * @return LeagueMatch
     */
    public function setChallengerScore(Score $challengerScore = null)
    {
        $this->challenger_score = $challengerScore;

        return $this;
    }

    /**
     * Get challenger_score
     *
     * @return Score
     */
    public function getChallengerScore()
    {
        return $this->challenger_score;
    }

    /**
     * Set challengee_score
     *
     * @param Score $challengeeScore
     *
     * @return LeagueMatch
     */
    public function setChallengeeScore(Score $challengeeScore = null)
    {
        $this->challengee_score = $challengeeScore;

        return $this;
    }

    /**
     * Get challengee_score
     *
     * @return Score
     */
    public function getChallengeeScore()
    {
        return $this->challengee_score;
    }

    /**
     * Add proofs
     *
     * @param LeagueMatchProof $proofs
     *
     * @return LeagueMatch
     */
    public function addProof(LeagueMatchProof $proofs)
    {
        $this->proofs[] = $proofs;

        return $this;
    }

    /**
     * Remove proofs
     *
     * @param LeagueMatchProof $proofs
     */
    public function removeProof(LeagueMatchProof $proofs)
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
