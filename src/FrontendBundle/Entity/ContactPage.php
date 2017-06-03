<?php

namespace FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Entity\Realtor;

/**
 * MenuItem
 *
 * @ORM\Table(name="front_contactpages")
 * @ORM\Entity(repositoryClass="FrontendBundle\Repository\MenuItemRepository")
 */
class ContactPage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * One Page can be used by many realtors.
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Realtor", mappedBy="realtor")
     */
    private $realtors;
    
    /**
     * Constructor for ContactPage
     */
    public function __construct() {
        $this->realtors = new ArrayCollection();
    }
    
    /**
     * Get Id
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection|Realtor[]
     */
    public function getRealtors() {
        return $this->realtors;
    }
    
}

