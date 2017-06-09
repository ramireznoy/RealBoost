<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;
use AdminBundle\Entity\State;

/**
 * BusinessWorker
 *
 * @ORM\Table(name="core_businessworkers")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ClientRepository")
 */
class BusinessWorker extends SystemUser {

    /**
     * @var \AdminBundle\Entity\State
     * 
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\State")
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
     * @var \Doctrine\Common\Collections\Collection|Service[]
     * 
     * @ORM\OneToMany(targetEntity="Service", mappedBy="worker") 
     */
    private $services;


    /**
     * Constructor for BusinessWorker
     */
    public function __construct() {
        parent::__construct();
        $this->services = new ArrayCollection();
    }
    
    /**
     * Get state
     * 
     * @return \AdminBundle\Entity\State
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Set state
     * 
     * @param \AdminBundle\Entity\State $state
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
     * @return BusinessWorker
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

    /**
     * Get services
     * 
     * @return \Doctrine\Common\Collections\Collection|Service[]
     */
    public function getServices() {
        return $this->services;
    }
}
