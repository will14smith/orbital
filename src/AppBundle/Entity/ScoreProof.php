<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="score_proof")
 */
class ScoreProof extends ProofEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Score", inversedBy="proof")
     */
    protected $score;

    /**
     * Set score
     *
     * @param \AppBundle\Entity\Score $score
     *
     * @return ScoreProof
     */
    public function setScore(Score $score = null)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return \AppBundle\Entity\Score
     */
    public function getScore()
    {
        return $this->score;
    }
}
