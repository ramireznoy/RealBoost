<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;
use AdminBundle\Entity\State;

/**
 * BusinessWorker
 *
 * @ORM\Table(name="core_businessworkers")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\BusinessWorkerRepository")
 */
class BusinessWorker extends SystemUser {

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
     */
    private $state;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=true)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=10)
     */
    private $zip;
    
    /**
     * Every user will can use many templates.
     * 
     * @ORM\ManyToMany(targetEntity="CardTemplate", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_businessworker_templates",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $templates;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Contact[]
     *
     * @ORM\ManyToMany(targetEntity="Contact", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_businessworker_contacts",
     *      joinColumns={@ORM\JoinColumn(name="businessworker_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $contacts;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Agency[]
     *
     * @ORM\ManyToMany(targetEntity="Agency", mappedBy="templates")
     */
    private $agencies;

    /**
     * Constructor for BusinessWorker
     */
    public function __construct() {
        parent::__construct();
        $this->templates = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->agencies = new ArrayCollection();
    }
    
    /**
     * Get state
     * 
     * @return State
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Set state
     * 
     * @param State $state
     * @return Client
     */
    public function setState(State $state) {
        $this->state = $state;
        
        return $this;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Client
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * Set city
     * 
     * @param srtring $city
     * @return Client
     */
    public function setCity($city) {
        $this->city = $city;
        
        return $this;
    }
    
    /**
     * Get city
     * 
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Client
     */
    public function setZip($zip) {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }
    
    /**
     * Get templates
     * 
     * @return \Doctrine\Common\Collections\Collection|CardTemplate[]
     */
    public function getCardTemplates() {
        return $this->templates;
    }

    /**
     * @param CardTemplate $template
     */
    public function addCardTemplate(CardTemplate $template) {
        if ($this->templates->contains($template)) {
            return;
        }
        $this->templates->add($template);
    }

    /**
     * @param CardTemplate $template
     */
    public function removeCardTemplate(CardTemplate $template) {
        if (!$this->templates->contains($template)) {
            return;
        }
        $this->templates->removeElement($template);
    }
    
    /**
     * Get all contacts
     * 
     * @return \Doctrine\Common\Collections\Collection|Contact[]
     */
    public function getContacts() {
        return $this->contacts;
    }

    /**
     * @param Contact $contact
     */
    public function addContact(Contact $contact) {
        if ($this->contacts->contains($contact)) {
            return;
        }
        $this->contacts->add($contact);
    }

    /**
     * @param Contact $contact
     */
    public function removeContact(Contact $contact) {
        if (!$this->contacts->contains($contact)) {
            return;
        }
        $this->contacts->removeElement($contact);
    }
    
    /**
     * Get user's agencies
     * 
     * @return \Doctrine\Common\Collections\Collection|Agency[]
     */
    public function getAgencies() {
        return $this->agencies;
    }
}
