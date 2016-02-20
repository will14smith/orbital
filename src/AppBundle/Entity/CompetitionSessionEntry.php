<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CompetitionSessionEntryRepository")
 * @ORM\Table(name="competition_entry")
 */
class CompetitionSessionEntry
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_entered;

    /**
     * Could be null if reserved for club
     *
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;
    /**
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;
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
     * @ORM\ManyToOne(targetEntity="CompetitionSession", inversedBy="entries")
     */
    protected $session;
    /**
     * @ORM\ManyToOne(targetEntity="Round")
     */
    protected $round;


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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $registered;
    /**
     * @ORM\OneToOne(targetEntity="Score", inversedBy="competition_entry")
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
     * Set bowtype
     *
     * @param string $bowtype
     * @return CompetitionSessionEntry
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
     * @return CompetitionSessionEntry
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
     * @return CompetitionSessionEntry
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
     * Set date_approved
     *
     * @param \DateTime $dateApproved
     * @return CompetitionSessionEntry
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
     * @return CompetitionSessionEntry
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
     * @return CompetitionSessionEntry
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
     * Set registered
     *
     * @param \DateTime $registered
     * @return CompetitionSessionEntry
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
    
        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime 
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set person
     *
     * @param Person $person
     * @return CompetitionSessionEntry
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;
    
        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set club
     *
     * @param Club $club
     * @return CompetitionSessionEntry
     */
    public function setClub(Club $club = null)
    {
        $this->club = $club;
    
        return $this;
    }

    /**
     * Get club
     *
     * @return Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set session
     *
     * @param CompetitionSession $session
     * @return CompetitionSessionEntry
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
     * @return CompetitionSessionEntry
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
     * Set score
     *
     * @param Score $score
     * @return CompetitionSessionEntry
     */
    public function setScore(Score $score = null)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return Score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set date_entered
     *
     * @param \DateTime $dateEntered
     * @return CompetitionSessionEntry
     */
    public function setDateEntered($dateEntered)
    {
        $this->date_entered = $dateEntered;
    
        return $this;
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
}