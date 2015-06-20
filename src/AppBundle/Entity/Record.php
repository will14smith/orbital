<?php


namespace AppBundle\Entity;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RecordRepository")
 * @ORM\Table(name="record")
 */
class Record
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="records")
     */
    protected $round;
    /**
     * @ORM\Column(type="integer")
     */
    protected $num_holders = 1;
    /**
     * @ORM\Column(type="string")
     */
    protected $skill;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bowtype;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender;

    /**
     * @ORM\OneToMany(targetEntity="RecordHolder", mappedBy="record")
     * @ORM\OrderBy({"date" = "DESC", "score" = "DESC"})
     */
    protected $holders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->holders = new ArrayCollection();
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
     * Set num_holders
     *
     * @param integer $numHolders
     *
     * @return Record
     */
    public function setNumHolders($numHolders)
    {
        $this->num_holders = $numHolders;

        return $this;
    }

    /**
     * Get num_holders
     *
     * @return integer
     */
    public function getNumHolders()
    {
        return $this->num_holders;
    }

    /**
     * Set skill
     *
     * @param string $skill
     *
     * @return Record
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
     * @return Record
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
     * Set gender
     *
     * @param string $gender
     *
     * @return Record
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
     *
     * @return Record
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

    /**
     * Add holders
     *
     * @param \AppBundle\Entity\RecordHolder $holder
     *
     * @return Record
     */
    public function addHolder(RecordHolder $holder)
    {
        $this->holders[] = $holder;

        return $this;
    }

    /**
     * Remove holders
     *
     * @param \AppBundle\Entity\RecordHolder $holders
     */
    public function removeHolder(RecordHolder $holders)
    {
        $this->holders->removeElement($holders);
    }

    /**
     * Get confirmed holders
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolder[]
     */
    public function getHolders()
    {
        return $this->holders->filter(function(RecordHolder $item) {
            return $item->getDateConfirmed() != null;
        });
    }

    /**
     * Get unconfirmed holders
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolder[]
     */
    public function getUnconfirmedHolders()
    {
        return $this->holders->filter(function(RecordHolder $item) {
            return $item->getDateConfirmed() == null;
        });
    }

    /**
     * Get all holders
     *
     * @return \Doctrine\Common\Collections\Collection|RecordHolder[]
     */
    public function getAllHolders()
    {
        return $this->holders;
    }

    /**
     * @return RecordHolder
     */
    public function getCurrentHolder()
    {
        // the array is ordered by date then score, exactly what we want
        return $this->getHolders()->first();
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        // skill gender? bowtype? round team?
        $name = Skill::display($this->skill);

        if ($this->gender) {
            $name .= ' ' . Gender::display($this->gender);
        }

        if ($this->bowtype) {
            $name .= ' ' . BowType::display($this->bowtype);
        }

        $name .= ' ' . $this->getRound()->getName();

        if ($this->num_holders > 1) {
            $name .= ' Team';
        }

        return $name;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
