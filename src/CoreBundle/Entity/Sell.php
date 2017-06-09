<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\State;

/**
 * Service
 *
 * @ORM\Table(name="core_sales")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\SellRepository")
 */
class Sell
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
     * @var bool
     *
     * @ORM\Column(name="completed", type="boolean")
     */
    private $completed;
    
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
     * @var \AdminBundle\Entity\State
     *
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
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
     * @var datetime
     *
     * @ORM\Column(name="registered", type="datetime")
     */
    private $registered;
    
    /**
     * @var int
     *
     * @ORM\Column(name="calification", type="integer", nullable=true)
     */
    private $calification;
    
    /**
     * @var string
     *
     * @ORM\Column(name="client_comments", type="string", length=200, nullable=true)
     */
    private $clientComments;
    
    /**
     * @var string
     *
     * @ORM\Column(name="worker_observations", type="string", length=200, nullable=true)
     */
    private $workerObservations;
    
    /**
     * @ORM\ManyToMany(targetEntity="Merchandise", inversedBy="sales")
     * @ORM\JoinTable(name="relation_sell_merchandise",
     *      joinColumns={@ORM\JoinColumn(name="sell_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="merchandise_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $items;
    
    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @var BusinessWorker
     *
     * @ORM\ManyToOne(targetEntity="BusinessWorker")
     * @ORM\JoinColumn(name="worker", referencedColumnName="id")
     */
    private $worker;
    
    
    public function __construct() {
        $this->registered = new \DateTime();
        $this->items = new ArrayCollection();
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
     * Set completed
     *
     * @param boolean $completed
     *
     * @return Schedule
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
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
     * @return Service
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
     * @return Schedule
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
     * @return Schedule
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
     * @return Schedule
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
     * @return Service
     */
    public function setZip($zip) {
        $this->zip = $zip;
        
        return $this;
    }

    /**
     * Set State
     * 
     * @param \CoreBundle\Entity\State $state
     * @return Service
     */
    public function setState(State $state) {
        $this->state = $state;
        
        return $this;
    }

    /**
     * Set city
     * 
     * @param srtring $city
     * @return Service
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
     * @return Service
     */
    public function setScheduled($datetime) {
        $this->scheduled = $datetime;
        
        return $this;
    }
    
    /**
     * Get registration datetime
     * 
     * @return datetime
     */
    public function getRegistered() {
        return $this->registered;
    }
    
    /**
     * Get calification
     * 
     * @return int
     */
    public function getCalification() {
        return $this->calification;
    }
    
    /**
     * Get client comments
     * 
     * @return string
     */
    public function getClientComments() {
        return $this->clientComments;
    }

    /**
     * Get worker observations
     * 
     * @return string
     */
    public function getWorkerObservations() {
        return $this->workerObservations;
    }

    /**
     * Set calification
     * 
     * @param int $calification
     * @return Service
     */
    public function setCalification($calification) {
        $this->calification = $calification;
        
        return $this;
    }

    /**
     * Set client comments
     * 
     * @param string $clientComments
     * @return Service
     */
    public function setClientComments($clientComments) {
        $this->clientComments = $clientComments;
        
        return $this;
    }

    /**
     * Set worker observations
     * 
     * @param string $workerObservations
     * @return Service
     */
    public function setWorkerObservations($workerObservations) {
        $this->workerObservations = $workerObservations;
        
        return $this;
    }
        
    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection|Merchandise[]
     */
    public function getItems() {
        return $this->items;
    }
    
    /**
     * Add Item
     * 
     * @param Merchandise $item
     * @return Sell
     */
    public function addItem(Merchandise $item) {
        if ($this->items->contains($item)) {
            return $this;
        }
        $this->items->add($item);
        return $this;
    }

    /**
     * Remove Item
     * 
     * @param Merchandise $item
     */
    public function removeItem(Merchandise $item) {
        if (!$this->items->contains($item)) {
            return;
        }
        $this->items->removeElement($item);
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
     * @return Service
     */
    public function setClient(Client $client) {
        $this->client = $client;
        
        return $this;
    }
    
    /**
     * Set worker
     *
     * @param BusinessWorker $worker
     *
     * @return Service
     */
    public function setWorker(BusinessWorker $worker) {
        $this->worker = $worker;

        return $this;
    }

    /**
     * Get worker
     *
     * @return BusinessWorker
     */
    public function getWorker() {
        return $this->worker;
    }
    
    /**
     * Is service assigned already?
     * 
     * @return boolean
     */
    public function isAssigned() {
        if ($this->worker == null) {
            return false;
        }
        return true;
    }
    
    /**
     * Get total price
     * 
     * @return decimal
     */
    public function getTotalPrice() {
        $price = 0.0;
        foreach ($this->items as $item) {
            $price += $item->getPrice();
        }
        return $price;
    }
}

