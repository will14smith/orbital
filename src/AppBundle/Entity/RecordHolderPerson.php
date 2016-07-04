<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_holder_person")
 */
class RecordHolderPerson
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RecordHolder", inversedBy="people")
     */
    protected $record_holder;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;
    /**
     * @ORM\Column(type="integer")
     */
    protected $score_value;
    /**
     * @ORM\ManyToOne(targetEntity="Score")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $score;

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
     * Set score_value.
     *
     * @param int $scoreValue
     *
     * @return RecordHolderPerson
     */
    public function setScoreValue($scoreValue)
    {
        $this->score_value = $scoreValue;

        return $this;
    }

    /**
     * Get score_value.
     *
     * @return int
     */
    public function getScoreValue()
    {
        return $this->score_value;
    }

    /**
     * Set record_holder.
     *
     * @param \AppBundle\Entity\RecordHolder $recordHolder
     *
     * @return RecordHolderPerson
     */
    public function setRecordHolder(RecordHolder $recordHolder = null)
    {
        $this->record_holder = $recordHolder;

        return $this;
    }

    /**
     * Get record_holder.
     *
     * @return \AppBundle\Entity\RecordHolder
     */
    public function getRecordHolder()
    {
        return $this->record_holder;
    }

    /**
     * Set person.
     *
     * @param \AppBundle\Entity\Person $person
     *
     * @return RecordHolderPerson
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set score.
     *
     * @param \AppBundle\Entity\Score $score
     *
     * @return RecordHolderPerson
     */
    public function setScore(Score $score = null)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score.
     *
     * @return \AppBundle\Entity\Score
     */
    public function getScore()
    {
        return $this->score;
    }
}
