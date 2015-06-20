<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BadgeSheetRepository")
 * @ORM\Table(name="badge_sheet")
 */
class BadgeSheet
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\ManyToMany(targetEntity="BadgeHolder")
     */
    protected $badgeHolders;

    /**
     * Constructor
     *
     * @param BadgeHolder[] $badges
     */
    public function __construct(array $badges = [])
    {
        $this->badgeHolders = new ArrayCollection();

        $this->date = new \DateTime();
        foreach($badges as $badge) {
            $this->addBadgeHolder($badge);
        }
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
     * Set date
     *
     * @param \DateTime $date
     * @return BadgeSheet
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add badgeHolders
     *
     * @param BadgeHolder $badgeHolders
     * @return BadgeSheet
     */
    public function addBadgeHolder(BadgeHolder $badgeHolders)
    {
        $this->badgeHolders[] = $badgeHolders;
    
        return $this;
    }

    /**
     * Remove badgeHolders
     *
     * @param BadgeHolder $badgeHolders
     */
    public function removeBadgeHolder(BadgeHolder $badgeHolders)
    {
        $this->badgeHolders->removeElement($badgeHolders);
    }

    /**
     * Get badgeHolders
     *
     * @return \Doctrine\Common\Collections\Collection|BadgeHolder[]
     */
    public function getBadgeHolders()
    {
        return $this->badgeHolders;
    }
}
