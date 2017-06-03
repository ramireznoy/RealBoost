<?php

namespace FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuItem
 *
 * @ORM\Table(name="front_contactpages")
 * @ORM\Entity(repositoryClass="FrontendBundle\Repository\MenuItemRepository")
 */
class ContacPage
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
     * Get Id
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }
    
}

