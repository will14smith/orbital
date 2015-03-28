<?php
/**
 * Created by PhpStorm.
 * User: will_000
 * Date: 28/03/2015
 * Time: 00:30
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="badge_holder_proof")
 */
class BadgeHolderProof {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="BadgeHolder", inversedBy="proof")
     */
    protected $badge_holder;
    /**
     * @ORM\ManyToOne(targetEntity="Person")
     */
    protected $person;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image_name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

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
     * Set image_name
     *
     * @param string $imageName
     * @return BadgeHolderProof
     */
    public function setImageName($imageName)
    {
        $this->image_name = $imageName;

        return $this;
    }

    /**
     * Get image_name
     *
     * @return string 
     */
    public function getImageName()
    {
        return $this->image_name;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return BadgeHolderProof
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set badge_holder
     *
     * @param \AppBundle\Entity\BadgeHolder $badgeHolder
     * @return BadgeHolderProof
     */
    public function setBadgeHolder(\AppBundle\Entity\BadgeHolder $badgeHolder = null)
    {
        $this->badge_holder = $badgeHolder;

        return $this;
    }

    /**
     * Get badge_holder
     *
     * @return \AppBundle\Entity\BadgeHolder 
     */
    public function getBadgeHolder()
    {
        return $this->badge_holder;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return BadgeHolderProof
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
}
