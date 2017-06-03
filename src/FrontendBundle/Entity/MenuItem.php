<?php

namespace FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MenuItem
 *
 * @ORM\Table(name="front_menuitems")
 * @ORM\Entity(repositoryClass="FrontendBundle\Repository\MenuItemRepository")
 */
class MenuItem
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
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=30, nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="path_name", type="string", length=255, nullable=true)
     */
    private $pathName;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="ajaxload", type="boolean")
     */
    private $ajaxload;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|MenuItem[]
     * 
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     */
    private $children;

    /**
     * @var MenuItem
     * 
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;
    

    public function __construct() {
        $this->children = new ArrayCollection();
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
     * @return MenuItem
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
     * Set icon
     *
     * @param string $icon
     *
     * @return MenuItem
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set pathName
     *
     * @param string $pathName
     *
     * @return MenuItem
     */
    public function setPathName($pathName)
    {
        $this->pathName = $pathName;

        return $this;
    }

    /**
     * Get pathName
     *
     * @return string
     */
    public function getPathName()
    {
        return $this->pathName;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return MenuItem
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return MenuItem
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    /*
     * Is ajaxload
     * 
     * $return bool
     */
    public function isAjaxload() {
        return $this->ajaxload;
    }

    /**
     * Set ajaxload
     * 
     * @param type $ajaxload
     * @return MenuItem
     */
    public function setAjaxload($ajaxload) {
        $this->ajaxload = $ajaxload;
        
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|MenuItem[]
     */
    public function getChildren() {
        return $this->children;
    }
    
    /**
     * Get parent
     *
     * @return MenuItem
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent(MenuItem $parent) {
        $this->parent = $parent;
        
        return $this;
    }
}

