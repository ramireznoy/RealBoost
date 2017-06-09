<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Service
 *
 * @ORM\Table(name="core_services")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ServiceRepository")
 */
class Service
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="extra", type="boolean", options={"default" : false})
     */
    private $extra;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;
    
    /**
     * One Service has Many merchandises.
     * @ORM\OneToMany(targetEntity="Merchandise", mappedBy="service")
     */
    private $merchandises;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->merchandises = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Merchandise
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
     * Set description
     *
     * @param string $description
     *
     * @return Merchandise
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Get extra
     * 
     * @return bool
     */
    public function isExtra() {
        return $this->extra;
    }

    /**
     * Set extra
     * 
     * @param bool $extra
     * @return Service
     */
    public function setExtra($extra) {
        $this->extra = $extra;
        
        return $this;
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
     * Get salespercent
     * @return float
     */
    public function getSalesCount() {
        $count = 0;
        foreach($this->merchandises as $m) {
            $count += $m->getSalesCount();
        }
        return $count;
    }
}

