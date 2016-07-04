<?php

namespace AppBundle\Entity;

use AppBundle\Services\Enum\Skill;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="people")
     * @ORM\JoinColumn(name="club_id", referencedColumnName="id", nullable=false)
     */
    protected $club;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name; // Official Name

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $name_preferred;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $agb_number;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $cid;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $mobile;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $gender;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_of_birth;

    /**
     * @ORM\Column(type="date")
     * The date when the person started archery.
     * Used for calculating novice / senior
     */
    protected $date_started;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $bowtype;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $club_bow;

    /**
     * @ORM\OneToMany(targetEntity="PersonHandicap", mappedBy="person")
     */
    protected $handicaps;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->handicaps = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Person
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name_preferred.
     *
     * @param string $namePreferred
     *
     * @return Person
     */
    public function setNamePreferred($namePreferred)
    {
        $this->name_preferred = $namePreferred;

        return $this;
    }

    /**
     * Get name_preferred.
     *
     * @return string
     */
    public function getNamePreferred()
    {
        return $this->name_preferred;
    }

    public function getDisplayName()
    {
        if ($this->name_preferred) {
            return $this->name_preferred;
        }

        return $this->name;
    }

    /**
     * Set agb_number.
     *
     * @param string $agbNumber
     *
     * @return Person
     */
    public function setAgbNumber($agbNumber)
    {
        $this->agb_number = $agbNumber;

        return $this;
    }

    /**
     * Get agb_number.
     *
     * @return string
     */
    public function getAgbNumber()
    {
        return $this->agb_number;
    }

    /**
     * Set cid.
     *
     * @param string $cid
     *
     * @return Person
     */
    public function setCid($cid)
    {
        $this->cid = $cid;

        return $this;
    }

    /**
     * Get cid.
     *
     * @return string
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Set mobile.
     *
     * @param string $mobile
     *
     * @return Person
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile.
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = strtolower($gender);

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender()
    {
        return strtolower($this->gender);
    }

    /**
     * Set date_of_birth.
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Person
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->date_of_birth = $dateOfBirth;

        return $this;
    }

    /**
     * Get date_of_birth.
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * Set bowtype.
     *
     * @param string $bowtype
     *
     * @return Person
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
     * Set club_bow.
     *
     * @param string $clubBow
     *
     * @return Person
     */
    public function setClubBow($clubBow)
    {
        $this->club_bow = $clubBow;

        return $this;
    }

    /**
     * Get club_bow.
     *
     * @return string
     */
    public function getClubBow()
    {
        return $this->club_bow;
    }

    /**
     * Set club.
     *
     * @param \AppBundle\Entity\Club $club
     *
     * @return Person
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
     * Add handicaps.
     *
     * @param PersonHandicap $handicaps
     *
     * @return Person
     */
    public function addHandicap(PersonHandicap $handicaps)
    {
        $this->handicaps[] = $handicaps;

        return $this;
    }

    /**
     * Remove handicaps.
     *
     * @param PersonHandicap $handicaps
     */
    public function removeHandicap(PersonHandicap $handicaps)
    {
        $this->handicaps->removeElement($handicaps);
    }

    /**
     * Get handicaps.
     *
     * @return \Doctrine\Common\Collections\Collection|PersonHandicap[]
     */
    public function getHandicaps()
    {
        return $this->handicaps;
    }

    public function __toString()
    {
        if ($this->name_preferred) {
            return sprintf('%s (%s)', $this->name, $this->name_preferred);
        }

        return $this->name;
    }

    public function isAdmin()
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function getSkillOn(\DateTime $date)
    {
        return Skill::getSkillOn($this->getDateStarted(), $date);
    }

    public function getCurrentSkill()
    {
        return $this->getSkillOn(new \DateTime('now'));
    }

    /**
     * Set dateStarted.
     *
     * @param \DateTime $dateStarted
     *
     * @return Person
     */
    public function setDateStarted($dateStarted)
    {
        $this->date_started = $dateStarted;

        return $this;
    }

    /**
     * Get dateStarted.
     *
     * @return \DateTime
     */
    public function getDateStarted()
    {
        return $this->date_started;
    }
}
