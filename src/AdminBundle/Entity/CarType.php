<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarType
 *
 * @ORM\Table(name="admin_cartypes")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\CarTypeRepository")
 */
class CarType
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
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;


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
     * @return CarType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get car type name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return CarType
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
        
        return $this;
    }
}

