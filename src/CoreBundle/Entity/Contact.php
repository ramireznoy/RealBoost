<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\State;

/**
 * Client
 *
 * @ORM\Table(name="core_contacts")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ContactRepository")
 */
class Contact {

    /**
     * @var int
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
     * @ORM\Column(name="phone", type="string", length=20, unique=true)
     */
    private $phone;
    
    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
     */
    private $state;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=true)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10)
     */
    private $zip;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default" : false})
     */
    private $active;

    /**
     * Constructor for Contact
     */
    public function __construct() {
    }
    
    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Contact
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Contact
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }
    
    /**
     * Get full name
     *
     * @return string
     */
    public function getFullname() {
        return $this->firstname.' '.$this->lastname;
    }
    
        /**
     * Set email
     *
     * @param string $email
     *
     * @return Contact
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Contact
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    
    /**
     * Get state
     * 
     * @return State
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Set state
     * 
     * @param State $state
     * @return Contact
     */
    public function setState(State $state) {
        $this->state = $state;
        
        return $this;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Contact
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * Set city
     * 
     * @param srtring $city
     * @return Contact
     */
    public function setCity($city) {
        $this->city = $city;
        
        return $this;
    }
    
    /**
     * Get city
     * 
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Contact
     */
    public function setZip($zip) {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }
    
    /**
     * Set status
     *
     * @param boolean $active
     *
     * @return Contact
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function isActive() {
        return $this->active;
    }
}
