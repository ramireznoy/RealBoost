<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CarBrand
 *
 * @ORM\Table(name="admin_carbrands")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\CarBrandRepository")
 */
class CarBrand
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20)
     */
    private $name;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|BrandModel[]
     * 
     * @ORM\OneToMany(targetEntity="BrandModel", mappedBy="brand") 
     */
    private $models;
    
    
    public function __construct() {
        $this->models = new ArrayCollection();
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
     * @return CarBrands
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
     * Get models
     * 
     * @return \Doctrine\Common\Collections\Collection|BrandModel[]
     */
    public function getModels() {
        return $this->models;
    }
}

