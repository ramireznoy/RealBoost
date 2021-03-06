<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use AdminBundle\Constants\CRoles;

/**
 * SystemUser
 *
 * @ORM\Table(name="admin_systemusers")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SystemUserRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="user_type", type="string")
 * @ORM\DiscriminatorMap(
 *     {"system"="SystemUser", "agency"="\CoreBundle\Entity\Agency", "worker"="\CoreBundle\Entity\BusinessWorker"}
 * )
 */
class SystemUser implements AdvancedUserInterface, \Serializable {

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
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, unique=true)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="accesstoken", type="string", length=255, nullable=true)
     */
    private $accesstoken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="datetime")
     */
    private $registered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogin", type="datetime", nullable=true)
     */
    private $lastlogin;

    /**
     * @var \Doctrine\Common\Collections\Collection|UserGroup[]
     *
     * @ORM\ManyToMany(targetEntity="UserGroup", mappedBy="users")
     */
    private $groups;
    
    /**
     * One Frequency has Many Schedules.
     * @ORM\OneToMany(targetEntity="CalendarBundle\Entity\Schedule", mappedBy="user")
     */
    private $schedule;

    /**
     * Constructor for SystemUser
     */
    public function __construct() {
        $this->groups = new ArrayCollection();
        $this->registered = new \DateTime();
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
     * Set username
     *
     * @param string $username
     *
     * @return SystemUser
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return SystemUser
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return SystemUser
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
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return SystemUser
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return SystemUser
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }
    
    /**
     * Get full name
     *
     * @return string
     */
    public function getFullname() {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return SystemUser
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return SystemUser
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    
    /**
     * Get photo
     * 
     * @return string
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * Set photo
     * 
     * @param string $photo
     * @return SystemUser
     */
    public function setPhoto($photo) {
        $this->photo = $photo;
        return $this;
    }

    /**
     * Set accesstoken
     *
     * @param string $accesstoken
     *
     * @return SystemUser
     */
    public function setAccesstoken($accesstoken) {
        $this->accesstoken = $accesstoken;

        return $this;
    }

    /**
     * Get accesstoken
     *
     * @return string
     */
    public function getAccesstoken() {
        return $this->accesstoken;
    }

    /**
     * Set registered
     *
     * @param \DateTime $registered
     *
     * @return SystemUser
     */
    public function setRegistered($registered) {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime
     */
    public function getRegistered() {
        return $this->registered;
    }

    /**
     * Set lastlogin
     *
     * @param \DateTime $lastlogin
     *
     * @return SystemUser
     */
    public function setLastlogin($lastlogin) {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    /**
     * Get lastlogin
     *
     * @return \DateTime
     */
    public function getLastlogin() {
        return $this->lastlogin;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|UserGroup[]
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * Add Group
     * 
     * @param UserGroup $group
     * @return SystemUser
     */
    public function addGroup(UserGroup $group) {
        if ($this->groups->contains($group)) {
            return $this;
        }
        $this->groups->add($group);
        $group->addUser($this);
        return $this;
    }

    /**
     * @param UserGroup $group
     */
    public function removeGroup(UserGroup $group) {
        if (!$this->groups->contains($group)) {
            return;
        }
        $this->groups->removeElement($group);
    }

    public function eraseCredentials() {
        $this->plainPassword = null;
    }

    public function getRoles() {
        $roles = array();
        foreach ($this->groups as $g) {
            $roles[] = $g->getRole();
        }
        return $roles;
    }

    public function getSalt() {
        return null;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    /**
     * Get enabled state
     * 
     * @return boolean
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->enabled
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->password,
                $this->email,
                $this->enabled
                ) = unserialize($serialized);
    }
    
    public function getUserType() {
        $response = array();
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            switch ($role) {
                case 'ROLE_WORK':
                    $response[] = 'Worker';
                    break;
                case 'ROLE_AUDITORY':
                    $response[] = 'Auditor';
                    break;
                case 'ROLE_MANAGEMENT':
                    $response[] = 'Manager';
                    break;
                case 'ROLE_ADMINISTRATION':
                    $response[] = 'Administrator';
                    break;
            }
        }
        if (count($response) == 0) {
            $response[] = 'Cliente';
        }
        return implode(',', $response);
    }
    
    public function getGroup() {
        return $this->groups[0];
    }
}
