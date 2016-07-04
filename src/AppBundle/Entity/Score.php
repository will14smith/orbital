<?php

namespace AppBundle\Entity;

use AppBundle\Constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ScoreRepository")
 * @ORM\Table(name="score")
 * @ORM\HasLifecycleCallbacks
 */
class Score
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;
    /**
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $bowtype;

    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;

    /**
     * @ORM\Column(type="integer")
     */
    protected $score;
    /**
     * @ORM\Column(type="integer")
     */
    protected $golds;
    /**
     * @ORM\Column(type="integer")
     */
    protected $hits;

    /**
     * @ORM\ManyToOne(targetEntity="Competition")
     */
    protected $competition;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_shot;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_entered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_accepted;

    /**
     * @ORM\OneToMany(targetEntity="ScoreProof", mappedBy="score")
     */
    protected $proof;

    /** @ORM\PrePersist */
    public function on_create()
    {
        //using Doctrine DateTime here
        $this->date_entered = new \DateTime('now');
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bowtype.
     *
     * @param string $bowtype
     *
     * @return Score
     */
    public function setBowtype($bowtype)
    {
        $this->bowtype = strtolower($bowtype);

        return $this;
    }

    /**
     * Get bowtype.
     *
     * @return string
     */
    public function getBowtype()
    {
        return strtolower($this->bowtype);
    }

    /**
     * Set score.
     *
     * @param int $score
     *
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set golds.
     *
     * @param int $golds
     *
     * @return Score
     */
    public function setGolds($golds)
    {
        $this->golds = $golds;

        return $this;
    }

    /**
     * Get golds.
     *
     * @return int
     */
    public function getGolds()
    {
        return $this->golds;
    }

    /**
     * Set hits.
     *
     * @param int $hits
     *
     * @return Score
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Get hits.
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set competition.
     *
     * @param Competition $competition
     *
     * @return Score
     */
    public function setCompetition(Competition $competition = null)
    {
        $this->competition = $competition;

        return $this;
    }

    /**
     * Get competition.
     *
     * @return Competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Set date_shot.
     *
     * @param \DateTime $dateShot
     *
     * @return Score
     */
    public function setDateShot($dateShot)
    {
        $this->date_shot = $dateShot;

        return $this;
    }

    /**
     * Get date_shot.
     *
     * @return \DateTime
     */
    public function getDateShot()
    {
        return $this->date_shot;
    }

    /**
     * Set date_entered.
     *
     * @deprecated
     *
     * @param \DateTime $dateEntered
     *
     * @return Score
     *
     * @throws \Exception
     */
    public function setDateEntered($dateEntered)
    {
        throw new \Exception('this function does nothing...');
    }

    /**
     * Get date_entered.
     *
     * @return \DateTime
     */
    public function getDateEntered()
    {
        return $this->date_entered;
    }

    /**
     * Set date_accepted.
     *
     * @param \DateTime $dateAccepted
     *
     * @return Score
     */
    public function setDateAccepted(\DateTime $dateAccepted = null)
    {
        $this->date_accepted = $dateAccepted;

        return $this;
    }

    /**
     * Get date_accepted.
     *
     * @return \DateTime
     */
    public function getDateAccepted()
    {
        return $this->date_accepted;
    }

    /**
     * Set the score as accepted.
     *
     * @return Score
     */
    public function accept()
    {
        if (!$this->date_accepted) {
            $this->date_accepted = new \DateTime('now');
        }

        return $this;
    }

    public function isAccepted(\DateTime $now = null)
    {
        if ($now === null) {
            $now = new \DateTime('now');
        }

        return $this->getDateAccepted() && $this->getDateAccepted() <= $now;
    }

    /**
     * Set person.
     *
     * @param \AppBundle\Entity\Person $person
     *
     * @return Score
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set club.
     *
     * @param \AppBundle\Entity\Club $club
     *
     * @return Score
     */
    public function setClub(Club $club = null)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club.
     *
     * @return \AppBundle\Entity\Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set round.
     *
     * @param \AppBundle\Entity\Round $round
     *
     * @return Score
     */
    public function setRound(Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round.
     *
     * @return \AppBundle\Entity\Round
     */
    public function getRound()
    {
        return $this->round;
    }

    public function __toString()
    {
        return sprintf('%s - %s - %s - %d',
            $this->getDateShot()->format(Constants::DATE_FORMAT),
            $this->getPerson()->getName(),
            $this->getRound()->getName(),
            $this->getScore()
        );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->arrows = new ArrayCollection();
        $this->proof = new ArrayCollection();
    }

    /**
     * Add proof.
     *
     * @param \AppBundle\Entity\ScoreProof $proof
     *
     * @return Score
     */
    public function addProof(ScoreProof $proof)
    {
        $this->proof[] = $proof;

        return $this;
    }

    /**
     * Remove proof.
     *
     * @param \AppBundle\Entity\ScoreProof $proof
     */
    public function removeProof(ScoreProof $proof)
    {
        $this->proof->removeElement($proof);
    }

    /**
     * Get proof.
     *
     * @return \Doctrine\Common\Collections\Collection|ScoreProof[]
     */
    public function getProof()
    {
        return $this->proof;
    }

    /**
     * @return bool
     */
    public function isIndoor()
    {
        return $this->getRound()->getIndoor();
    }

    /**
     * @param Score $other
     *
     * @return bool
     */
    public function isBetterThan(Score $other)
    {
        return $this->getScore() > $other->getScore();
    }

    public function getSkill()
    {
        return $this->getPerson()->getSkillOn($this->getDateShot());
    }
}
