<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;
use AdminBundle\Entity\State;

/**
 * Client
 *
 * @ORM\Table(name="core_agencies")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\AgencyRepository")
 */
class Agency extends SystemUser {

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=255, nullable=true)
     */
    private $business_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_title", type="string", length=255, nullable=true)
     */
    private $business_title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_phone", type="string", length=20, nullable=true)
     */
    private $business_phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_logo", type="string", nullable=true)
     */
    private $business_logo;
    
    /**
     * @var \AdminBundle\Entity\State
     * 
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\State")
     * @ORM\JoinColumn(name="business_state", referencedColumnName="id")
     */
    private $business_state;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_address", type="string", length=255, nullable=true)
     */
    private $business_address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_city", type="string", length=50, nullable=true)
     */
    private $business_city;

    /**
     * @var string
     *
     * @ORM\Column(name="business_zip", type="string", length=10, nullable=true)
     */
    private $business_zip;

    /**
     * Every user will can use many templates.
     * 
     * @ORM\ManyToMany(targetEntity="CardTemplate", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_agency_templates",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $templates;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Contact[]
     *
     * @ORM\ManyToMany(targetEntity="Contact", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_agency_businessworkers",
     *      joinColumns={@ORM\JoinColumn(name="agency_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="businessworker_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $workers;

    /**
     * Constructor for Agency
     */
    public function __construct() {
        parent::__construct();
        $this->templates = new ArrayCollection();
        $this->workers = new ArrayCollection();
    }
    
    /**
     * Get business name
     * 
     * @return string
     */
    public function getBusinessName() {
        return $this->business_name;
    }

    /**
     * Get business title or position
     * 
     * @return string
     */
    public function getBusinessTitle() {
        return $this->business_title;
    }

    /**
     * Get business phone
     * 
     * @return string
     */
    public function getBusinessPhone() {
        return $this->business_phone;
    }

    /**
     * Set business name
     * 
     * @param string $business_name
     * @return Agency
     */
    public function setBusinessName($business_name) {
        $this->business_name = $business_name;
        return $this;
    }

    /**
     * Set business title or position
     * 
     * @param string $business_title
     * @return Agency
     */
    public function setBusinessTitle($business_title) {
        $this->business_title = $business_title;
        return $this;
    }

    /**
     * Set business phone
     * 
     * @param string $business_phone
     * @return Agency
     */
    public function setBusinessPhone($business_phone) {
        $this->business_phone = $business_phone;
        return $this;
    }
    
    /**
     * Get business logo
     * 
     * @return string
     */
    public function getBusinessLogo() {
        return $this->business_logo;
    }

    /**
     * Set business logo
     * 
     * @param string $business_logo
     * @return Agency
     */
    public function setBusinessLogo($business_logo) {
        $this->business_logo = $business_logo;
        return $this;
    }
            
    /**
     * Get business_state
     * 
     * @return \AdminBundle\Entity\State
     */
    public function getBusinessState() {
        return $this->business_state;
    }
    
    /**
     * Set business_state
     * 
     * @param \AdminBundle\Entity\State $business_state
     * @return Agency
     */
    public function setBusinessState(State $business_state) {
        $this->business_state = $business_state;
        
        return $this;
    }

    /**
     * Set business_address
     *
     * @param string $business_address
     *
     * @return Agency
     */
    public function setBusinessAddress($business_address) {
        $this->business_address = $business_address;

        return $this;
    }

    /**
     * Get business_address
     *
     * @return string
     */
    public function getBusinessAddress() {
        return $this->business_address;
    }
    
    /**
     * Set business_city
     * 
     * @param srtring $business_city
     * @return Agency
     */
    public function setBusinessCity($business_city) {
        $this->business_city = $business_city;
        
        return $this;
    }
    
    /**
     * Get business_city
     * 
     * @return string
     */
    public function getBusinessCity() {
        return $this->business_city;
    }

    /**
     * Set business_zip
     *
     * @param string $business_zip
     *
     * @return Agency
     */
    public function setBusinessZip($business_zip) {
        $this->business_zip = $business_zip;

        return $this;
    }

    /**
     * Get business_zip
     *
     * @return string
     */
    public function getBusinessZip() {
        return $this->business_zip;
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
     * Get all workers
     * 
     * @return \Doctrine\Common\Collections\Collection|BusinessWorker[]
     */
    public function getWorkers() {
        return $this->workers;
    }

    /**
     * Add a worker
     * 
     * @param BusinessWorker $worker
     */
    public function addWorker(BusinessWorker $worker) {
        if ($this->workers->contains($worker)) {
            return;
        }
        $this->workers->add($worker);
    }

    /**
     * Remove a worker
     * 
     * @param BusinessWorker $worker
     */
    public function removeWorker(BusinessWorker $worker) {
        if (!$this->workers->contains($worker)) {
            return;
        }
        $this->workers->removeElement($worker);
    }
}
