<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\CarType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Merchandise
 *
 * @ORM\Table(name="core_merchandises")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\MerchandiseRepository")
 */
class Merchandise
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
     * @ORM\Column(name="price", type="decimal", precision=15, scale=2)
     */
    private $price;
    
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3)
     */
    private $currency;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;
    
    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="merchandises")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $service;
    
    /**
     * @var CarType
     *
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\CarType")
     * @ORM\JoinColumn(name="car_type", referencedColumnName="id")
     */
    private $cartype;
    
    /**
     * One Merchandise has Many sales.
     * @ORM\ManyToMany(targetEntity="Sell", mappedBy="items")
     */
    private $sales;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->sales = new ArrayCollection();
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
     * Set sku
     *
     * @param string $sku
     *
     * @return Merchandise
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Merchandise
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Merchandise
     */
    public function setCurrency($currency) {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }
    
    /**
     * get enabled
     * 
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Set enabled
     * 
     * @param bool $enabled
     * @return Merchandise
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
        
        return $this;
    }
    
    /**
     * Set service
     *
     * @param Service $service
     *
     * @return Merchandise
     */
    public function setService($service) {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return Service
     */
    public function getService() {
        return $this->service;
    }
    
    /**
     * Get car type
     * 
     * @return cartype
     */
    public function getCartype() {
        return $this->cartype;
    }

    /**
     * Set car type
     * 
     * @param \AdminBundle\Entity\CarType $cartype
     * @return Merchandise
     */
    public function setCartype(CarType $cartype) {
        $this->cartype = $cartype;
        
        return $this;
    }
    
    /**
     * Get sales
     * 
     * @return Doctrine\Common\Collections\ArrayCollection|Sell
     */
    public function getSalesCount() {
        return count($this->sales);
    }
}

