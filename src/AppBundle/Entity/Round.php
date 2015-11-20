<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoundRepository")
 * @ORM\Table(name="round")
 */
class Round implements JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RoundCategory", inversedBy="rounds")
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="RoundTarget", mappedBy="round", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    protected $targets;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $indoor;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="round")
     */
    protected $records;

    public function __construct()
    {
        $this->targets = new ArrayCollection();
        $this->records = new ArrayCollection();
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
     *
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
     *
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
     * @return \Doctrine\Common\Collections\Collection|RoundTarget[]
     */
    public function getTargets()
    {
        return $this->targets;
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Add records
     *
     * @param \AppBundle\Entity\Record $records
     *
     * @return Round
     */
    public function addRecord(Record $records)
    {
        $this->records[] = $records;

        return $this;
    }

    /**
     * Remove records
     *
     * @param \AppBundle\Entity\Record $records
     */
    public function removeRecord(Record $records)
    {
        $this->records->removeElement($records);
    }

    /**
     * Get records
     *
     * @return \Doctrine\Common\Collections\Collection|Record[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    public function getTotalArrows()
    {
        return array_sum($this->getTargets()->map(function (RoundTarget $target) {
            return $target->getArrowCount();
        })->toArray());
    }

    function jsonSerialize()
    {
        $targets = $this->getTargets()->map(function (RoundTarget $value) {
            return $value->jsonSerialize();
        })->toArray();

        return [
            'name' => $this->name,
            'total_arrows' => $this->getTotalArrows(),
            'targets' => $targets,
        ];
    }

    /**
     * Set category
     *
     * @param RoundCategory $category
     * @return Round
     */
    public function setCategory(RoundCategory $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return RoundCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set indoor
     *
     * @param boolean $indoor
     * @return Round
     */
    public function setIndoor($indoor)
    {
        $this->indoor = $indoor;
    
        return $this;
    }

    /**
     * Get indoor
     *
     * @return boolean 
     */
    public function getIndoor()
    {
        return $this->indoor;
    }
}
