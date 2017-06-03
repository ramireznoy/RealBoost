<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\State;

/**
 * Payment
 *
 * @ORM\Table(name="core_payments")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\PaymentRepository")
 */
class Payment
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
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;
    
    /**
     * @var string
     *
     * @ORM\Column(name="debt", type="decimal", precision=10, scale=2)
     */
    private $debt;

    /**
     * @var bool
     *
     * @ORM\Column(name="payed", type="boolean")
     */
    private $payed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=9, scale=6)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=9, scale=6)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=8)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=100)
     */
    private $state;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50)
     */
    private $city;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="scheduled", type="datetime")
     */
    private $scheduled;
    
    /**
     * @var int
     *
     * @ORM\Column(name="weekspayed", type="integer")
     */
    private $weekspayed;
    
    /**
     * @var int
     *
     * @ORM\Column(name="weekstopay", type="integer")
     */
    private $weekstopay;
    
    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="charges")
     * @ORM\JoinColumn(name="client", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @var Advisor
     *
     * @ORM\ManyToOne(targetEntity="Advisor", inversedBy="payments")
     * @ORM\JoinColumn(name="advisor", referencedColumnName="id")
     */
    private $advisor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="confirmation", type="string", length=50)
     */
    private $confirmation;
    
    
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
     * Is service payed?
     * 
     * @return boolean
     */
    public function isPayed() {
        return $this->payed;
    }

    /**
     * Set payed state
     * 
     * @param boolean $payed
     * @return Payment
     */
    public function setPayed($payed) {
        $this->payed = $payed;
        
        return $this;
    }
    
    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return string
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return string
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return string
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * Get Zip code
     * 
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * get State
     * 
     * @return \AdminBundle\Entity\State
     */
    public function getState() {
        return $this->state;
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
     * Set zip code
     * 
     * @param string $zip
     * @return Payment
     */
    public function setZip($zip) {
        $this->zip = $zip;
        
        return $this;
    }

    /**
     * Set State
     * 
     * @param \CoreBundle\Entity\State $state
     * @return Payment
     */
    public function setState(State $state) {
        $this->state = $state;
        
        return $this;
    }

    /**
     * Set city
     * 
     * @param srtring $city
     * @return Payment
     */
    public function setCity($city) {
        $this->city = $city;
        
        return $this;
    }
        
    /**
     * Get scheduled datetime
     * 
     * @return datetime
     */
    public function getScheduled() {
        return $this->scheduled;
    }
    
    /**
     * Set schedule datetime
     * 
     * @param datetime $datetime
     * @return Payment
     */
    public function setScheduled($datetime) {
        $this->scheduled = $datetime;
        
        return $this;
    }
    
    /**
     * Get Client
     * 
     * @return Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set Client
     * 
     * @param Client $client
     * @return Payment
     */
    public function setClient(Client $client) {
        $this->client = $client;
        
        return $this;
    }
    
    /**
     * Set advisor
     *
     * @param Advisor $advisor
     *
     * @return Payment
     */
    public function setAdvisor(Advisor $advisor) {
        $this->advisor = $advisor;

        return $this;
    }

    /**
     * Get advisor
     *
     * @return Advisor
     */
    public function getAdvisor() {
        return $this->advisor;
    }
    
    public function getDebt() {
        return $this->debt;
    }
    
    public function setDebt($debt) {
        $this->debt = $debt;
        return $this;
    }
    
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }
    
    public function getAmount() {
        return $this->amount;
    }
    
    public function setWeekspayed($weekspayed) {
        $this->weekspayed = $weekspayed;
        return $this;
    }

    public function setWeekstopay($weekstopay) {
        $this->weekstopay = $weekstopay;
        return $this;
    }
    
    public function getWeekspayed() {
        return $this->weekspayed;
    }

    public function getWeekstopay() {
        return $this->weekstopay;
    }
    
    public function getConfirmation() {
        return $this->confirmation;
    }

    public function setConfirmation($confirmation) {
        $this->confirmation = $confirmation;
        return $this;
    }
}

