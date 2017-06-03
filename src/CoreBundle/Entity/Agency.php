<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;

/**
 * Agency
 *
 * @ORM\Table(name="core_agencies")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\AgencyRepository")
 */
class Agency extends SystemUser {

    /**
     * @var string
     *
     * @ORM\Column(name="coordinator", type="string", length=255, unique=true)
     */
    private $coordinator;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|RealtorGroup[]
     * 
     * One Agency has Many RealtorGroups.
     * @ORM\OneToMany(targetEntity="RealtorGroup", mappedBy="agency", cascade={"persist", "remove"})
     */
    private $clientgroups;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Payment[]
     * 
     * One Agency has Many Payments.
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="agency", cascade={"persist", "remove"})
     */
    private $payments;


    /**
     * Constructor for Agency
     */
    public function __construct() {
        parent::__construct();
        $this->clientgroups = new ArrayCollection();
    }
    
    /**
     * Get coordinator
     *
     * @return string
     */
    public function getCoordinator() {
        return $this->coordinator;
    }

    /**
     * Set coordinator
     * 
     * @return Agency
     */
    public function setCoordinator($coordinator) {
        $this->coordinator = $coordinator;
        
        return $this;
    }
    
    /**
     * Get client groups
     *
     * @return \Doctrine\Common\Collections\Collection|RealtorGroup[]
     */
    public function getRealtorGroups()
    {
        return $this->clientgroups;
    }
    
    /**
     * @param RealtorGroup $clientgroup
     */
    public function addRealtorGroup(RealtorGroup $clientgroup) {
        if ($this->clientgroups->contains($clientgroup)) {
            return;
        }
        $clientgroup->setPaymentgroup($this);
    }

    /**
     * @param RealtorGroup $clientgroup
     */
    public function removeRealtorGroup(RealtorGroup $clientgroup) {
        if (!$this->clientgroups->contains($clientgroup)) {
            return;
        }
        $clientgroup->setAgency($this);
        $this->clientgroups->removeElement($clientgroup);
    }
    
    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection|Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }
    
    /**
     * @param RealtorGroup $payment
     */
    public function addPayment(RealtorGroup $payment) {
        if ($this->payments->contains($payment)) {
            return;
        }
        $payment->setAgency($this);
        $this->payments->add($payment);
    }

    /**
     * @param RealtorGroup $payment
     */
    public function removePayment(RealtorGroup $payment) {
        if (!$this->payments->contains($payment)) {
            return;
        }
        $this->payments->removeElement($payment);
    }
}
