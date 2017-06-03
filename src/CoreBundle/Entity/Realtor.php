<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\SystemUser;
use Doctrine\Common\Collections\ArrayCollection;
use CoreBundle\Entity\Contact;

/**
 * Realtor
 *
 * @ORM\Table(name="core_realtors")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\RealtorRepository")
 */
class Realtor extends SystemUser {

    /**
     * @var RealtorGroup
     *
     * @ORM\ManyToOne(targetEntity="RealtorGroup",inversedBy="realtors")
     * @ORM\JoinColumn(name="agency", referencedColumnName="id")
     */
    private $realtorgroup;
    
    /**
     * @var ContactPage
     *
     * @ORM\ManyToOne(targetEntity="FrontendBundle\Entity\ContactPage",inversedBy="realtors")
     * @ORM\JoinColumn(name="contact_page", referencedColumnName="id")
     */
    private $contactpage;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Contacts[]
     *
     * @ORM\ManyToMany(targetEntity="Contact", mappedBy="realtors")
     */
    private $contacts;
    
    /**
     * One Realtor has Many Sales.
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="realtor")
     */
    private $sales;

    
    /**
     * Constructor for Realtor
     */
    public function __construct() {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->sales = new ArrayCollection();
    }
    
    /**
     * Add Contact
     * 
     * @param RealtorGroup $realtorgroup
     * @return Realtor
     */
    public function setGroup(RealtorGroup $realtorgroup) {
        $this->realtorgroup = $realtorgroup;
        
        return $this;
    }
    
    /**
     * Get Realtor group
     * 
     * @return RealtorGroup
     */
    public function getRealtorgroup() {
        return $this->realtorgroup;
    }

    /**
     * Set Realtor group
     * 
     * @param RealtorGroup $realtorgroup
     * @return Realtor
     */
    public function setRealtorgroup(RealtorGroup $realtorgroup) {
        $this->realtorgroup = $realtorgroup;
        
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection|Contact[]
     */
    public function getContacts() {
        return $this->contacts;
    }

    /**
     * Add Contact
     * 
     * @param Contact $contact
     * @return Realtor
     */
    public function addContact(Contact $contact) {
        if ($this->contacts->contains($contact)) {
            return $this;
        }
        $this->contacts->add($contact);
        $contact->addRealtor($this);
        return $this;
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
    
    public function getSales() {
        return $this->sales;
    }
    
    public function getUnpaidChargesCount() {
        $count = 0;
        foreach ($this->sales as $sell) {
            if (!$sell->isPayed()) {
                $count = $count + 1;
            }
        }
        return $count;
    }
}
