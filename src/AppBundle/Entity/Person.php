<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PersonRepository")
 * @ORM\Table(name="person")
 */
class Person implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    //TODO $club

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name; // Official Name

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $name_preferred;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $agb_number;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $cid;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $cuser;
    /**
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    protected $email;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $mobile;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $gender;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_of_birth;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $skill;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $bowtype;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $club_bow;

    /**
     * Only used for non-cusers
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $password;
    /**
     * Only used for non-cusers
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $salt;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $admin;

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
     * @return Person
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
     * Set name_preferred
     *
     * @param string $namePreferred
     * @return Person
     */
    public function setNamePreferred($namePreferred)
    {
        $this->name_preferred = $namePreferred;

        return $this;
    }

    /**
     * Get name_preferred
     *
     * @return string 
     */
    public function getNamePreferred()
    {
        return $this->name_preferred;
    }

    /**
     * Set agb_number
     *
     * @param string $agbNumber
     * @return Person
     */
    public function setAgbNumber($agbNumber)
    {
        $this->agb_number = $agbNumber;

        return $this;
    }

    /**
     * Get agb_number
     *
     * @return string 
     */
    public function getAgbNumber()
    {
        return $this->agb_number;
    }

    /**
     * Set cid
     *
     * @param string $cid
     * @return Person
     */
    public function setCid($cid)
    {
        $this->cid = $cid;

        return $this;
    }

    /**
     * Get cid
     *
     * @return string 
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Set cuser
     *
     * @param string $cuser
     * @return Person
     */
    public function setCuser($cuser)
    {
        $this->cuser = $cuser;

        return $this;
    }

    /**
     * Get cuser
     *
     * @return string 
     */
    public function getCuser()
    {
        return $this->cuser;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Person
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Person
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set date_of_birth
     *
     * @param \DateTime $dateOfBirth
     * @return Person
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->date_of_birth = $dateOfBirth;

        return $this;
    }

    /**
     * Get date_of_birth
     *
     * @return \DateTime 
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * Set skill
     *
     * @param string $skill
     * @return Person
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return string 
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set bowtype
     *
     * @param string $bowtype
     * @return Person
     */
    public function setBowtype($bowtype)
    {
        $this->bowtype = $bowtype;

        return $this;
    }

    /**
     * Get bowtype
     *
     * @return string 
     */
    public function getBowtype()
    {
        return $this->bowtype;
    }

    /**
     * Set club_bow
     *
     * @param string $clubBow
     * @return Person
     */
    public function setClubBow($clubBow)
    {
        $this->club_bow = $clubBow;

        return $this;
    }

    /**
     * Get club_bow
     *
     * @return string 
     */
    public function getClubBow()
    {
        return $this->club_bow;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     * @return Person
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        if($this->admin) {
            return ['ROLE_ADMIN'];
        } else {
            return ['ROLE_USER'];
        }
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->password = null;
        $this->salt = null;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Person
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }
}
