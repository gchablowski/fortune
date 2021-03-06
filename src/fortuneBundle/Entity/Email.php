<?php

namespace fortuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Email
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="fortuneBundle\Entity\EmailRepository")
 * @UniqueEntity("email")
 * @ORM\HasLifecycleCallbacks()
 */
class Email {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\Email(
     *     checkHost = true,
     *     checkMX = true)
     * @ORM\Column(name="email", type="string", length=500)
     */
    private $email;

    /**
     * @var string

     * @ORM\Column(name="token", type="string", length=500)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
     */
    private $create;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * contstructor
     */
    function __construct() {
        $this->active = false;
    }

    /**
     * Create token before persist
     * 
     * @ORM\PrePersist
     */
    public function setTokenValue() {
        $this->token = hash('sha256',$this->email);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Email
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Set create
     *
     * @param \DateTime $create
     * @return Email
     */
    public function setCreate($create) {
        $this->create = $create;

        return $this;
    }

    /**
     * Get create
     *
     * @return \DateTime 
     */
    public function getCreate() {
        return $this->create;
    }


    /**
     * Set token
     *
     * @param string $token
     * @return Email
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }
}
