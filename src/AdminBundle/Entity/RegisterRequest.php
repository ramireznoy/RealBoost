<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * RegisterRequest
 *
 * @ORM\Table(name="admin_registerrequests")
 * @UniqueEntity(fields="email", message="Email already registered")
 * @UniqueEntity(fields="mobile", message="Phone already registered")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\RegisterRequestRepository")
 */
class RegisterRequest
{
    /**
     * @var bigint
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=20, unique=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="url_token", type="string", length=128, unique=true)
     */
    private $urltoken;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $timestamp;
    
    
    public function __construct() {
        $this->timestamp = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return RegisterRequest
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return RegisterRequest
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return RegisterRequest
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
     * Get url token
     * 
     * @return string
     */
    public function getUrltoken() {
        return $this->urltoken;
    }

    /**
     * Set url token
     * 
     * @param string $urltoken
     * @return RegisterRequest
     */
    public function setUrltoken($urltoken) {
        $this->urltoken = $urltoken;
        
        return $this;
    }
    
    /**
     * Get request date
     * 
     * @return datetime
     */
    public function getDate() {
        return $this->date;
    }
    
    /**
     * Get Mobile phone
     * 
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     * Set Mobile number
     * 
     * @param string $mobile
     * @return RegisterRequest
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;
        return $this;
    }
    
}

