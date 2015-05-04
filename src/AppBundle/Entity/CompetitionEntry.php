<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competition_entry")
 */
class CompetitionEntry
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="entries")
     */
    protected $competition;

    /**
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $club;
    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;

    /**
     * @ORM\Column(type="string")
     */
    protected $bowtype;
    /**
     * @ORM\Column(type="string")
     */
    protected $skill;
    /**
     * @ORM\Column(type="string")
     */
    protected $gender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_approved;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $boss_number;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $target_number;

    /**
     * @ORM\OneToOne(targetEntity="Score")
     * @ORM\JoinColumn(name="score_id", referencedColumnName="id")
     **/
    protected $score;

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
     * Set date_approved
     *
     * @param \DateTime $dateApproved
     *
     * @return CompetitionEntry
     */
    public function setDateApproved($dateApproved)
    {
        $this->date_approved = $dateApproved;

        return $this;
    }

    /**
     * Get date_approved
     *
     * @return \DateTime
     */
    public function getDateApproved()
    {
        return $this->date_approved;
    }

    /**
     * Set boss_number
     *
     * @param integer $bossNumber
     *
     * @return CompetitionEntry
     */
    public function setBossNumber($bossNumber)
    {
        $this->boss_number = $bossNumber;

        return $this;
    }

    /**
     * Get boss_number
     *
     * @return integer
     */
    public function getBossNumber()
    {
        return $this->boss_number;
    }

    /**
     * Set target_number
     *
     * @param integer $targetNumber
     *
     * @return CompetitionEntry
     */
    public function setTargetNumber($targetNumber)
    {
        $this->target_number = $targetNumber;

        return $this;
    }

    /**
     * Get target_number
     *
     * @return integer
     */
    public function getTargetNumber()
    {
        return $this->target_number;
    }

    /**
     * Set competition
     *
     * @param \AppBundle\Entity\Competition $competition
     *
     * @return CompetitionEntry
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
     * Set club
     *
     * @param \AppBundle\Entity\Club $club
     *
     * @return CompetitionEntry
     */
    public function setClub(\AppBundle\Entity\Club $club = null)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return \AppBundle\Entity\Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     *
     * @return CompetitionEntry
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
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
     * Set score
     *
     * @param \AppBundle\Entity\Score $score
     *
     * @return CompetitionEntry
     */
    public function setScore(\AppBundle\Entity\Score $score = null)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return \AppBundle\Entity\Score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set bowtype
     *
     * @param string $bowtype
     * @return CompetitionEntry
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
     * Set skill
     *
     * @param string $skill
     * @return CompetitionEntry
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
     * @return CompetitionEntry
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
     * @return CompetitionEntry
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
