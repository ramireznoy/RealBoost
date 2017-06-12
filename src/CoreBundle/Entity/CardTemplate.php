<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CardTemplate
 *
 * @ORM\Table(name="core_cardtemplates")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CardTemplateRepository")
 */
class CardTemplate {

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", unique=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var \Doctrine\Common\Collections\Collection|BusinessWorker[]
     *
     * @ORM\ManyToMany(targetEntity="BusinessWorker", mappedBy="templates")
     */
    private $users;
    
    /**
     * Constructor for CardTemplate
     */
    public function __construct() {
        $this->users = new ArrayCollection();
    }
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CardTemplate
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CardTemplate
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return CardTemplate
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection|SystemWorker[]
     */
    public function getUsers() {
        return $this->users;
    }
}
