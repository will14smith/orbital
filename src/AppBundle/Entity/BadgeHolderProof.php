<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="badge_holder_proof")
 */
class BadgeHolderProof extends ProofEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="BadgeHolder", inversedBy="proof")
     */
    protected $badge_holder;

    /**
     * Set badge_holder.
     *
     * @param \AppBundle\Entity\BadgeHolder $badgeHolder
     *
     * @return BadgeHolderProof
     */
    public function setBadgeHolder(BadgeHolder $badgeHolder = null)
    {
        $this->badge_holder = $badgeHolder;

        return $this;
    }

    /**
     * Get badge_holder.
     *
     * @return \AppBundle\Entity\BadgeHolder
     */
    public function getBadgeHolder()
    {
        return $this->badge_holder;
    }
}
