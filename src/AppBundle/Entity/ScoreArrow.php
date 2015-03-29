<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="score_arrow", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="score_arrow_number_idx", columns={"score_id", "number"})
 * })
 * @UniqueEntity({"score", "number"})#
 * @ORM\HasLifecycleCallbacks
 */
class ScoreArrow
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Score", inversedBy="arrows")
     */
    protected $score;
    /**
     * @ORM\Column(type="integer")
     */
    protected $number;

    /**
     * @ORM\Column(type="string")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $input_by;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $input_time;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $edit_by;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $edit_time;

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
     * Set number
     *
     * @param integer $number
     * @return ScoreArrow
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return ScoreArrow
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set input_time
     *
     * @param \DateTime $inputTime
     * @return ScoreArrow
     */
    public function setInputTime($inputTime)
    {
        $this->input_time = $inputTime;

        return $this;
    }

    /**
     * Get input_time
     *
     * @return \DateTime 
     */
    public function getInputTime()
    {
        return $this->input_time;
    }

    /**
     * Set edit_time
     *
     * @param \DateTime $editTime
     * @return ScoreArrow
     */
    public function setEditTime($editTime)
    {
        $this->edit_time = $editTime;

        return $this;
    }

    /**
     * Get edit_time
     *
     * @return \DateTime 
     */
    public function getEditTime()
    {
        return $this->edit_time;
    }

    /**
     * Set score
     *
     * @param \AppBundle\Entity\Score $score
     * @return ScoreArrow
     */
    public function setScore(\AppBundle\Entity\Score $score = null)
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

    /**
     * Set input_by
     *
     * @param \AppBundle\Entity\Person $inputBy
     * @return ScoreArrow
     */
    public function setInputBy(\AppBundle\Entity\Person $inputBy = null)
    {
        $this->input_by = $inputBy;

        return $this;
    }

    /**
     * Get input_by
     *
     * @return \AppBundle\Entity\Person 
     */
    public function getInputBy()
    {
        return $this->input_by;
    }

    /**
     * Set edit_by
     *
     * @param \AppBundle\Entity\Person $editBy
     * @return ScoreArrow
     */
    public function setEditBy(\AppBundle\Entity\Person $editBy = null)
    {
        $this->edit_by = $editBy;

        return $this;
    }

    /**
     * Get edit_by
     *
     * @return \AppBundle\Entity\Person 
     */
    public function getEditBy()
    {
        return $this->edit_by;
    }
}
