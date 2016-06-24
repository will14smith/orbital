<?php


namespace AppBundle\Entity;

use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Records\RecordManager;
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
     * @ORM\OneToMany(targetEntity="RecordClub", mappedBy="record", cascade={"persist", "remove"})
     */
    protected $clubs;

    /**
     * @ORM\OneToMany(targetEntity="RecordRound", mappedBy="record", cascade={"persist", "remove"})
     */
    protected $rounds;
    /**
     * @ORM\Column(type="integer")
     */
    protected $num_holders = 1;

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
     * Add holders
     *
     * @param \AppBundle\Entity\RecordHolder $holder
     *
     * @return Record
     */
    public function addHolder(RecordHolder $holder)
    {
        $holder->setRecord($this);
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
     * @param $club
     * @return RecordHolder[]|\Doctrine\Common\Collections\Collection
     */
    public function getHolders(Club $club)
    {
        return RecordManager::getConfirmedHolders($this, $club);
    }

    /**
     * Get unconfirmed holders
     *
     * @param Club $club
     * @return RecordHolder[]|\Doctrine\Common\Collections\Collection
     */
    public function getUnconfirmedHolders(Club $club)
    {
        return RecordManager::getUnconfirmedHolders($this, $club);
    }

    /**
     * Get all holders
     *
     * @param Club $club
     * @return RecordHolder[]|\Doctrine\Common\Collections\Collection
     */
    public function getAllHolders(Club $club)
    {
        return $this->holders->filter(function (RecordHolder $holder) use ($club) {
            return $holder->getClub()->getId() == $club->getId();
        });
    }

    /**
     * @param Club $club
     * @return RecordHolder
     * @throws \Exception
     */
    public function getCurrentHolder(Club $club)
    {
        return RecordManager::getCurrentHolder($this, $club);
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        // NOTE: keep AppBundle:Record:matrix.html.twig:format_record in sync
        return RecordManager::toString($this);
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * Add round
     *
     * @param RecordRound $round
     *
     * @return Record
     */
    public function addRound(RecordRound $round)
    {
        $round->setRecord($this);
        $this->rounds[] = $round;

        return $this;
    }

    /**
     * Remove round
     *
     * @param RecordRound $round
     */
    public function removeRound(RecordRound $round)
    {
        $this->rounds->removeElement($round);
    }

    /**
     * Get rounds
     *
     * @return \Doctrine\Common\Collections\Collection|RecordRound[]
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Add club
     *
     * @param RecordClub $club
     *
     * @return Record
     */
    public function addClub(RecordClub $club)
    {
        $club->setRecord($this);
        $this->clubs[] = $club;

        return $this;
    }

    /**
     * Remove club
     *
     * @param RecordClub $club
     */
    public function removeClub(RecordClub $club)
    {
        $this->clubs->removeElement($club);
    }

    /**
     * Get clubs
     *
     * @return \Doctrine\Common\Collections\Collection|RecordClub[]
     */
    public function getClubs()
    {
        return $this->clubs;
    }

    public function isIndoor()
    {
        return $this->getRounds()->forAll(function ($_, RecordRound $round) {
            return $round->getRound()->getIndoor();
        });
    }

    public function isNovice()
    {
        return $this->getRounds()->forAll(function($_, RecordRound $round) { return $round->getSkill() == Skill::NOVICE; });
    }

    public function getGender() {
        $rounds = $this->getRounds();

        if($rounds->count() == 0) {
            return null;
        }

        $primaryGender = $rounds[0]->getGender();
        $allGender = $rounds->forAll(function($_, RecordRound $round) use($primaryGender) { return $round->getGender() == $primaryGender; });

        return $allGender ? $primaryGender : null;
    }

    public function getBowtype()
    {
        $rounds = $this->getRounds();

        if($rounds->count() == 0) {
            return null;
        }

        $primaryBowtype = $rounds[0]->getBowtype();
        $all = $rounds->forAll(function($_, RecordRound $round) use($primaryBowtype) { return $round->getBowtype() == $primaryBowtype; });

        return $all ? $primaryBowtype : null;
    }
}
