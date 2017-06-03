<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SystemUser;

/**
 * Advisor
 *
 * @ORM\Table(name="core_advisors")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\AdvisorRepository")
 */
class Advisor extends SystemUser {

    /**
     * @var string
     *
     * @ORM\Column(name="coordinator", type="string", length=255, unique=true)
     */
    private $coordinator;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|ClientGroup[]
     * 
     * One Advisor has Many ClientGroups.
     * @ORM\OneToMany(targetEntity="ClientGroup", mappedBy="advisor", cascade={"persist", "remove"})
     */
    private $clientgroups;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Payment[]
     * 
     * One Advisor has Many Payments.
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="advisor", cascade={"persist", "remove"})
     */
    private $payments;


    /**
     * Constructor for Advisor
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
     * @return Advisor
     */
    public function setCoordinator($coordinator) {
        $this->coordinator = $coordinator;
        
        return $this;
    }
    
    /**
     * Get client groups
     *
     * @return \Doctrine\Common\Collections\Collection|ClientGroup[]
     */
    public function getClientGroups()
    {
        return $this->clientgroups;
    }
    
    /**
     * @param ClientGroup $clientgroup
     */
    public function addClientGroup(ClientGroup $clientgroup) {
        if ($this->clientgroups->contains($clientgroup)) {
            return;
        }
        $clientgroup->setPaymentgroup($this);
    }

    /**
     * @param ClientGroup $clientgroup
     */
    public function removeClientGroup(ClientGroup $clientgroup) {
        if (!$this->clientgroups->contains($clientgroup)) {
            return;
        }
        $clientgroup->setAdvisor($this);
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
     * @param ClientGroup $payment
     */
    public function addPayment(ClientGroup $payment) {
        if ($this->payments->contains($payment)) {
            return;
        }
        $payment->setAdvisor($this);
        $this->payments->add($payment);
    }

    /**
     * @param ClientGroup $payment
     */
    public function removePayment(ClientGroup $payment) {
        if (!$this->payments->contains($payment)) {
            return;
        }
        $this->payments->removeElement($payment);
    }
}
