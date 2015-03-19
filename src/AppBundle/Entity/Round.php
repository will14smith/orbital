<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="round")
 */
class Round {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="RoundTarget", mappedBy="round", cascade={"persist", "remove"})
     */
    protected $targets;

    public function __construct()
    {
        $this->targets = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Round
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add targets
     *
     * @param \AppBundle\Entity\RoundTarget $target
     * @return Round
     */
    public function addTarget(RoundTarget $target)
    {
        $target->setRound($this);
        $this->targets[] = $target;

        return $this;
    }

    /**
     * Remove targets
     *
     * @param \AppBundle\Entity\RoundTarget $target
     */
    public function removeTarget(RoundTarget $target)
    {
        $this->targets->removeElement($target);
    }

    /**
     * Get targets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTargets()
    {
        return $this->targets;
    }

    public function __toString() {
        return $this->name;
    }
}
