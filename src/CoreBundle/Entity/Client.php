<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;
use AdminBundle\Entity\State;

/**
 * Client
 *
 * @ORM\Table(name="core_clients")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ClientRepository")
 */
class Client extends SystemUser {

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
     * Constructor for Client
     */
    public function __construct() {
        parent::__construct();
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
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * @return Client
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
}
