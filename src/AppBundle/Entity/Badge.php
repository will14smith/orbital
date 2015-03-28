<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="badge")
 */
class Badge
{
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
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $algo_name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $category;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $multiple;

    /**
     * @ORM\OneToMany(targetEntity="BadgeHolder", mappedBy="badge")
     */
    protected $holders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->holders = new ArrayCollection();
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
     * @return Badge
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
     * Set description
     *
     * @param string $description
     *
     * @return Badge
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set algo_name
     *
     * @param string $algoName
     *
     * @return Badge
     */
    public function setAlgoName($algoName)
    {
        $this->algo_name = $algoName;

        return $this;
    }

    /**
     * Get algo_name
     *
     * @return string
     */
    public function getAlgoName()
    {
        return $this->algo_name;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Badge
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     *
     * @return Badge
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Add holders
     *
     * @param \AppBundle\Entity\BadgeHolder $holders
     *
     * @return Badge
     */
    public function addHolder(BadgeHolder $holders)
    {
        $this->holders[] = $holders;

        return $this;
    }

    /**
     * Remove holders
     *
     * @param \AppBundle\Entity\BadgeHolder $holders
     */
    public function removeHolder(BadgeHolder $holders)
    {
        $this->holders->removeElement($holders);
    }

    /**
     * Get holders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHolders()
    {
        return $this->holders;
    }

    public function __toString()
    {
        return $this->name;
    }
}
