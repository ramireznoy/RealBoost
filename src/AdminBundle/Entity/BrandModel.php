<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BrandModel
 *
 * @ORM\Table(name="admin_brandmodels")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BrandModelRepository")
 */
class BrandModel
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;
    
    /**
     * @var CarBrand
     * 
     * @ORM\ManyToOne(targetEntity="CarBrand")
     * @ORM\JoinColumn(name="brand", referencedColumnName="id")
     */
    private $brand;
    
    /**
     * @var CarType
     *
     * @ORM\ManyToOne(targetEntity="CarType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;
    
    
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
     * @return BrandModel
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
     * Get Brand
     * 
     * @return Brand
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Set Brand
     * 
     * @param CarBrand $brand
     * @return BrandModel
     */
    public function setBrand(CarBrand $brand) {
        $this->brand = $brand;
        
        return $this;
    }
    
    /**
     * Get car type
     * 
     * @return CarType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set car type
     * 
     * @param CarType $type
     * @return BrandModel
     */
    public function setType(CarType $type) {
        $this->type = $type;
        
        return $this;
    }
    
    public function getFullName() {
        return $this->brand->getName().' '.$this->name;
    }
}

