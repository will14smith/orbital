<?php


namespace AppBundle\Entity;

use AppBundle\Services\Enum\BadgeState;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BadgeHolderRepository")
 * @ORM\Table(name="badge_holder")
 */
class BadgeHolder
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Badge", inversedBy="holders")
     */
    protected $badge;

    /**
     * TODO inverse
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_awarded;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_confirmed;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_made;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_delivered;

    /**
     * @ORM\OneToMany(targetEntity="BadgeHolderProof", mappedBy="badge_holder")
     */
    protected $proof;

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
     * Set date_awarded
     *
     * @param \DateTime $dateAwarded
     * @return BadgeHolder
     */
    public function setDateAwarded($dateAwarded)
    {
        $this->date_awarded = $dateAwarded;

        return $this;
    }

    /**
     * Get date_awarded
     *
     * @return \DateTime 
     */
    public function getDateAwarded()
    {
        return $this->date_awarded;
    }

    /**
     * Set date_confirmed
     *
     * @param \DateTime $dateConfirmed
     * @return BadgeHolder
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
     * Set date_made
     *
     * @param \DateTime $dateMade
     * @return BadgeHolder
     */
    public function setDateMade($dateMade)
    {
        $this->date_made = $dateMade;

        return $this;
    }

    /**
     * Get date_made
     *
     * @return \DateTime 
     */
    public function getDateMade()
    {
        return $this->date_made;
    }

    /**
     * Set date_delivered
     *
     * @param \DateTime $dateDelivered
     * @return BadgeHolder
     */
    public function setDateDelivered($dateDelivered)
    {
        $this->date_delivered = $dateDelivered;

        return $this;
    }

    /**
     * Get date_delivered
     *
     * @return \DateTime 
     */
    public function getDateDelivered()
    {
        return $this->date_delivered;
    }

    /**
     * Set badge
     *
     * @param \AppBundle\Entity\Badge $badge
     * @return BadgeHolder
     */
    public function setBadge(\AppBundle\Entity\Badge $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get badge
     *
     * @return \AppBundle\Entity\Badge 
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return BadgeHolder
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
     * @return string State of badge
     */
    public function getState()
    {
        if(!$this->date_confirmed) {
            return BadgeState::UNCONFIRMED;
        }

        if(!$this->date_made) {
            return BadgeState::CONFIRMED;
        }

        if(!$this->date_delivered) {
            return BadgeState::MADE;
        }

        return BadgeState::DELIVERED;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proof = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add proof
     *
     * @param \AppBundle\Entity\BadgeHolderProof $proof
     * @return BadgeHolder
     */
    public function addProof(\AppBundle\Entity\BadgeHolderProof $proof)
    {
        $this->proof[] = $proof;

        return $this;
    }

    /**
     * Remove proof
     *
     * @param \AppBundle\Entity\BadgeHolderProof $proof
     */
    public function removeProof(\AppBundle\Entity\BadgeHolderProof $proof)
    {
        $this->proof->removeElement($proof);
    }

    /**
     * Get proof
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProof()
    {
        return $this->proof;
    }
}
