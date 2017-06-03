<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\SystemUser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table(name="core_clients")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ClientRepository")
 */
class Client extends SystemUser {

    /**
     * @var string
     *
     * @ORM\Column(name="group_position", type="string", length=100, unique=false)
     */
    private $groupposition;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=true)
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;
    
    /**
     * @var string
     *
     * @ORM\Column(name="payment", type="decimal", precision=10, scale=2)
     */
    private $payment;
    
    /**
     * @var ClientGroup
     *
     * @ORM\ManyToOne(targetEntity="ClientGroup",inversedBy="clients")
     * @ORM\JoinColumn(name="paymentgroup", referencedColumnName="id")
     */
    private $paymentgroup;
    
    /**
     * One Client has Many Charges.
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="client")
     */
    private $charges;

    
    /**
     * Constructor for Client
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    /**
     * Get client position in the group
     * 
     * @return string
     */
    public function getGroupposition() {
        return $this->groupposition;
    }

    /**
     * Set client position in the group
     * 
     * @param type $groupposition
     * @return Client
     */
    public function setGroupposition($groupposition) {
        $this->groupposition = $groupposition;
        
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
    
    public function getPayment() {
        return $this->payment;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }
    
    public function getAmount() {
        return $this->amount;
    }

    public function setPayment($payment) {
        $this->payment = $payment;
        return $this;
    }

    /**
     * Get payment group
     * 
     * @return ClientGroup
     */
    public function getPaymentgroup() {
        return $this->paymentgroup;
    }

    /**
     * Set payment group
     * 
     * @param ClientGroup $paymentgroup
     * @return Client
     */
    public function setPaymentgroup(ClientGroup $paymentgroup) {
        $this->paymentgroup = $paymentgroup;
        
        return $this;
    }
    
    public function getCharges() {
        return $this->charges;
    }
    
    public function getUnpaidChargesCount() {
        $count = 0;
        foreach ($this->charges as $charge) {
            if (!$charge->isPayed()) {
                $count = $count + 1;
            }
        }
        return $count;
    }
}
