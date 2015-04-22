<?php


namespace AppBundle\Entity;

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
     * TODO Inverse
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $skill;
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
     * @ORM\Column(type="boolean")
     */
    protected $competition;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $complete;

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
     * @ORM\OneToMany(targetEntity="ScoreArrow", mappedBy="score")
     */
    protected $arrows;
    /**
     * @ORM\OneToMany(targetEntity="ScoreProof", mappedBy="score")
     */
    protected $proof;

    /** @ORM\PrePersist */
    function on_create()
    {
        //using Doctrine DateTime here
        $this->date_entered = new \DateTime('now');
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
     * Set skill
     *
     * @param string $skill
     *
     * @return Score
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
     * @return Score
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
     * Set score
     *
     * @param integer $score
     *
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set golds
     *
     * @param integer $golds
     *
     * @return Score
     */
    public function setGolds($golds)
    {
        $this->golds = $golds;

        return $this;
    }

    /**
     * Get golds
     *
     * @return integer
     */
    public function getGolds()
    {
        return $this->golds;
    }

    /**
     * Set hits
     *
     * @param integer $hits
     *
     * @return Score
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Get hits
     *
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set competition
     *
     * @param boolean $competition
     *
     * @return Score
     */
    public function setCompetition($competition)
    {
        $this->competition = $competition;

        return $this;
    }

    /**
     * Get competition
     *
     * @return boolean
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Set complete
     *
     * @param boolean $complete
     *
     * @return Score
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;

        return $this;
    }

    /**
     * Get complete
     *
     * @return boolean
     */
    public function getComplete()
    {
        return $this->complete;
    }

    /**
     * Set date_shot
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
     * Get date_shot
     *
     * @return \DateTime
     */
    public function getDateShot()
    {
        return $this->date_shot;
    }

    /**
     * Set date_entered
     *
     * @deprecated
     *
     * @param \DateTime $dateEntered
     *
     * @return Score
     * @throws \Exception
     */
    public function setDateEntered($dateEntered)
    {
        throw new \Exception("this function does nothing...");
    }

    /**
     * Get date_entered
     *
     * @return \DateTime
     */
    public function getDateEntered()
    {
        return $this->date_entered;
    }

    /**
     * Set date_accepted
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
     * Get date_accepted
     *
     * @return \DateTime
     */
    public function getDateAccepted()
    {
        return $this->date_accepted;
    }

    /**
     * Set the score as accepted
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

    /**
     * Set person
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
     * Get person
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set round
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
     * Get round
     *
     * @return \AppBundle\Entity\Round
     */
    public function getRound()
    {
        return $this->round;
    }

    public function __toString()
    {
        return sprintf('%s - %s - %s - %i',
            $this->getDateShot()->format('d/m/Y H:i'),
            $this->getPerson()->getName(),
            $this->getRound()->getName(),
            $this->getScore()
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proof = new ArrayCollection();
    }

    /**
     * Add proof
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
     * Remove proof
     *
     * @param \AppBundle\Entity\ScoreProof $proof
     */
    public function removeProof(ScoreProof $proof)
    {
        $this->proof->removeElement($proof);
    }

    /**
     * Get proof
     *
     * @return \Doctrine\Common\Collections\Collection|ScoreProof[]
     */
    public function getProof()
    {
        return $this->proof;
    }

    /**
     * Add arrows
     *
     * @param \AppBundle\Entity\ScoreArrow $arrows
     * @return Score
     */
    public function addArrow(\AppBundle\Entity\ScoreArrow $arrows)
    {
        $this->arrows[] = $arrows;

        return $this;
    }

    /**
     * Remove arrows
     *
     * @param \AppBundle\Entity\ScoreArrow $arrows
     */
    public function removeArrow(\AppBundle\Entity\ScoreArrow $arrows)
    {
        $this->arrows->removeElement($arrows);
    }

    /**
     * Get arrows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArrows()
    {
        return $this->arrows;
    }
}
