<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="club")
 */
class Club
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $website;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="club")
     */
    protected $people;

    /* Record Stuff */
    /**
     * @ORM\Column(type="string")
     */
    protected $records_title;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $records_image_url;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $records_preface;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $records_appendix;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

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
     * Set name.
     *
     * @param string $name
     *
     * @return Club
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set website.
     *
     * @param string $website
     *
     * @return Club
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Club
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add people.
     *
     * @param \AppBundle\Entity\Person $people
     *
     * @return Club
     */
    public function addPerson(Person $people)
    {
        $this->people[] = $people;

        return $this;
    }

    /**
     * Remove people.
     *
     * @param \AppBundle\Entity\Person $people
     */
    public function removePerson(Person $people)
    {
        $this->people->removeElement($people);
    }

    /**
     * Get people.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeople()
    {
        return $this->people;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set recordsTitle.
     *
     * @param string $recordsTitle
     *
     * @return Club
     */
    public function setRecordsTitle($recordsTitle)
    {
        $this->records_title = $recordsTitle;

        return $this;
    }

    /**
     * Get recordsTitle.
     *
     * @return string
     */
    public function getRecordsTitle()
    {
        return $this->records_title;
    }

    /**
     * Set recordsImageUrl.
     *
     * @param string $recordsImageUrl
     *
     * @return Club
     */
    public function setRecordsImageUrl($recordsImageUrl)
    {
        $this->records_image_url = $recordsImageUrl;

        return $this;
    }

    /**
     * Get recordsImageUrl.
     *
     * @return string
     */
    public function getRecordsImageUrl()
    {
        return $this->records_image_url;
    }

    /**
     * Set recordsPreface.
     *
     * @param string $recordsPreface
     *
     * @return Club
     */
    public function setRecordsPreface($recordsPreface)
    {
        $this->records_preface = $recordsPreface;

        return $this;
    }

    /**
     * Get recordsPreface.
     *
     * @return string
     */
    public function getRecordsPreface()
    {
        return $this->records_preface;
    }

    /**
     * Set recordsAppendix.
     *
     * @param string $recordsAppendix
     *
     * @return Club
     */
    public function setRecordsAppendix($recordsAppendix)
    {
        $this->records_appendix = $recordsAppendix;

        return $this;
    }

    /**
     * Get recordsAppendix.
     *
     * @return string
     */
    public function getRecordsAppendix()
    {
        return $this->records_appendix;
    }
}
