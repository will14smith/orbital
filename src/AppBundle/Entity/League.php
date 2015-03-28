<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="league")
 */
class League
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
     * @ORM\Column(type="string")
     */
    protected $description;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $algo_name;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $open_date;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $close_date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $skill_limit;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bowtype_limit;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender_limit;

    /**
     * @ORM\ManyToMany(targetEntity="Round")
     * @ORM\JoinTable(name="league_round",
     *      joinColumns={@ORM\JoinColumn(name="league_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="round_id", referencedColumnName="id")}
     *      )
     */
    protected $rounds;
    /**
     * @ORM\OneToMany(targetEntity="LeaguePerson", mappedBy="league")
     * @ORM\OrderBy({ "points"="DESC", "initial_position"="ASC", "date_added"="ASC" })
     */
    protected $people;
    /**
     * @ORM\OneToMany(targetEntity="LeagueMatch", mappedBy="league")
     */
    protected $matches;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rounds = new \Doctrine\Common\Collections\ArrayCollection();
        $this->people = new \Doctrine\Common\Collections\ArrayCollection();
        $this->matches = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return League
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
     * @return League
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
     * Set algo_name
     *
     * @param string $algoName
     *
     * @return League
     */
    public function setAlgoName($algoName)
    {
        $this->algo_name = $algoName;

        return $this;
    }

    /**
     * Get algo_name
     *
     * @return string
     */
    public function getAlgoName()
    {
        return $this->algo_name;
    }

    /**
     * Set open_date
     *
     * @param \DateTime $openDate
     *
     * @return League
     */
    public function setOpenDate($openDate)
    {
        $this->open_date = $openDate;

        return $this;
    }

    /**
     * Get open_date
     *
     * @return \DateTime
     */
    public function getOpenDate()
    {
        return $this->open_date;
    }

    /**
     * Set close_date
     *
     * @param \DateTime $closeDate
     *
     * @return League
     */
    public function setCloseDate($closeDate)
    {
        $this->close_date = $closeDate;

        return $this;
    }

    /**
     * Get close_date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->close_date;
    }

    /**
     * Set skill_limit
     *
     * @param string $skillLimit
     *
     * @return League
     */
    public function setSkillLimit($skillLimit)
    {
        $this->skill_limit = $skillLimit;

        return $this;
    }

    /**
     * Get skill_limit
     *
     * @return string
     */
    public function getSkillLimit()
    {
        return $this->skill_limit;
    }

    /**
     * Set gender_limit
     *
     * @param string $genderLimit
     *
     * @return League
     */
    public function setGenderLimit($genderLimit)
    {
        $this->gender_limit = $genderLimit;

        return $this;
    }

    /**
     * Get gender_limit
     *
     * @return string
     */
    public function getGenderLimit()
    {
        return $this->gender_limit;
    }

    /**
     * Add rounds
     *
     * @param \AppBundle\Entity\Round $rounds
     *
     * @return League
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Add people
     *
     * @param \AppBundle\Entity\LeaguePerson $people
     *
     * @return League
     */
    public function addPerson(\AppBundle\Entity\LeaguePerson $people)
    {
        $this->people[] = $people;

        return $this;
    }

    /**
     * Remove people
     *
     * @param \AppBundle\Entity\LeaguePerson $people
     */
    public function removePerson(\AppBundle\Entity\LeaguePerson $people)
    {
        $this->people->removeElement($people);
    }

    /**
     * Get people
     *
     * @return \Doctrine\Common\Collections\Collection|LeaguePerson[]
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function canSignUp(Person $person)
    {
        if ($this->getSkillLimit()) {
            if ($person->getSkill() != $this->getSkillLimit()) {
                return false;
            }
        }
        if ($this->getBowtypeLimit()) {
            if ($person->getBowtype()) {
                if ($person->getBowtype() != $this->getBowtypeLimit()) {
                    return false;
                }
            }
        }
        if ($this->getGenderLimit()) {
            if ($person->getGender()) {
                if ($person->getGender() != $this->getGenderLimit()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function isSignedUp(Person $person)
    {
        $person_id = $person->getId();

        return $this->getPeople()->exists(function ($i, LeaguePerson $lp) use ($person_id) {
            return $lp->getPerson()->getId() == $person_id;
        });
    }

    /**
     * @param LeaguePerson $person
     *
     * @return int
     */
    public function getPersonPosition(LeaguePerson $person)
    {
        return $this->getPeople()->indexOf($person);
    }

    /**
     * Add matches
     *
     * @param \AppBundle\Entity\LeagueMatch $matches
     *
     * @return League
     */
    public function addMatch(\AppBundle\Entity\LeagueMatch $matches)
    {
        $this->matches[] = $matches;

        return $this;
    }

    /**
     * Remove matches
     *
     * @param \AppBundle\Entity\LeagueMatch $matches
     */
    public function removeMatch(\AppBundle\Entity\LeagueMatch $matches)
    {
        $this->matches->removeElement($matches);
    }

    /**
     * Get matches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Set bowtype_limit
     *
     * @param string $bowtypeLimit
     *
     * @return League
     */
    public function setBowtypeLimit($bowtypeLimit)
    {
        $this->bowtype_limit = $bowtypeLimit;

        return $this;
    }

    /**
     * Get bowtype_limit
     *
     * @return string
     */
    public function getBowtypeLimit()
    {
        return $this->bowtype_limit;
    }
}
