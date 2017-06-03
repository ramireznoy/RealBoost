<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Entity\Realtor;

/**
 * Contact
 *
 * @ORM\Table(name="core_contacts")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="referrals", type="integer")
     */
    private $referrals;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Realtor[]
     *
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Realtor", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_contacts_realtors",
     *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="realtor_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $realtors;
    
    /**
     * Constructor for Contacts
     */
    public function __construct() {
        $this->realtors = new ArrayCollection();
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
     * @return Contact
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Contact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Contact
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set referrals
     *
     * @param integer $referrals
     *
     * @return Contact
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;

        return $this;
    }

    /**
     * Get referrals
     *
     * @return int
     */
    public function getReferrals()
    {
        return $this->referrals;
    }
    
    /**
     * Get Email
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set Email
     * 
     * @param string $email
     * @return $this
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Get realtors
     * 
     * @return \Doctrine\Common\Collections\Collection|Realtor[]
     */
    public function getRealtors() {
        return $this->realtors;
    }

    /**
     * @param Realtor $realtor
     */
    public function addRealtor(Realtor $realtor) {
        if ($this->realtors->contains($realtor)) {
            return;
        }
        $this->realtors->add($realtor);
    }

    /**
     * @param Realtor $realtor
     */
    public function removeRealtor(Realtor $realtor) {
        if (!$this->realtors->contains($realtor)) {
            return;
        }
        $this->realtors->removeElement($realtor);
    }
}

