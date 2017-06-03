<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserGroup
 *
 * @ORM\Table(name="admin_usergroups")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UserGroupRepository")
 */
class UserGroup {

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
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=50, unique=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;

    /**
     * @var \Doctrine\Common\Collections\Collection|SystemUser[]
     *
     * @ORM\ManyToMany(targetEntity="SystemUser", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_usergroups_systemusers",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $users;

    /**
     * Constructor for UserGroup
     */
    public function __construct() {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * @return UserGroup
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
     * Get role
     * 
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set role
     * 
     * @param string $role
     * @return UserGroup
     */
    public function setRole($role) {
        $this->role = $role;
        
        return $this;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return UserGroup
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
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
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return UserGroup
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Get users
     * 
     * @return \Doctrine\Common\Collections\Collection|SystemUser[]
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * @param SystemUser $user
     */
    public function addUser(SystemUser $user) {
        if ($this->users->contains($user)) {
            return;
        }
        $this->users->add($user);
    }

    /**
     * @param SystemUser $user
     */
    public function removeUser(SystemUser $user) {
        if (!$this->users->contains($user)) {
            return;
        }
        $this->users->removeElement($user);
    }
}
