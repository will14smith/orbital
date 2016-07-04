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
     * Set match.
     *
     * @param LeagueMatch $match
     *
     * @return LeagueMatchProof
     */
    public function setMatch(LeagueMatch $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match.
     *
     * @return LeagueMatch
     */
    public function getMatch()
    {
        return $this->match;
    }
}
