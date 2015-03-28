<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="league_match_proof")
 */
class LeagueMatchProof extends ProofEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="LeagueMatch", inversedBy="proofs")
     */
    protected $match;

    /**
     * Set match
     *
     * @param \AppBundle\Entity\LeagueMatch $match
     * @return LeagueMatchProof
     */
    public function setMatch(\AppBundle\Entity\LeagueMatch $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return \AppBundle\Entity\LeagueMatch 
     */
    public function getMatch()
    {
        return $this->match;
    }
}
