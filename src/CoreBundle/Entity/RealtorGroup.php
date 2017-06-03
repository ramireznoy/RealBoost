<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RealtorGroup
 *
 * @ORM\Table(name="core_realtorgroups")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\RealtorGroupRepository")
 */
class RealtorGroup {

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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="quota", type="decimal", precision=10, scale=2)
     */
    private $quota;

    /**
     * @var int
     * 
     * @ORM\Column(name="term", type="integer", nullable=false, options={"unsigned":true, "default":0})
     */
    protected $term;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reunion", type="datetime")
     */
    private $reunion;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=true)
     */
    private $address;

    /**
     * @var \Doctrine\Common\Collections\Collection|Realtor[]
     *
     * One RealtorGroup has Many Realtors.
     * @ORM\OneToMany(targetEntity="Realtor", mappedBy="paymentgroup", cascade={"persist", "remove"})
     */
    private $realtors;

    /**
     * @var Agency
     *
     * @ORM\ManyToOne(targetEntity="Agency", inversedBy="realtorgroups")
     * @ORM\JoinColumn(name="agency", referencedColumnName="id")
     */
    private $agency;

    /**
     * Constructor for RealtorGroup
     */
    public function __construct() {
        $this->realtors = new ArrayCollection();
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
     * @return RealtorGroup
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
     * Set amount
     *
     * @param string $amount
     *
     * @return RealtorGroup
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return RealtorGroup
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

    public function getQuota() {
        return $this->quota;
    }

    public function setQuota($quota) {
        $this->quota = $quota;
        return $this;
    }
    
    public function getTerm() {
        return $this->term;
    }

    public function setTerm($term) {
        $this->term = $term;
        return $this;
    }
    
    /**
     * Set reunion date time
     *
     * @param \DateTime $reunion
     *
     * @return RealtorGroup
     */
    public function setReunion($reunion) {
        $this->reunion = $reunion;

        return $this;
    }

    /**
     * Get reunion date time
     *
     * @return \DateTime
     */
    public function getReunion() {
        return $this->reunion;
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
        $realtor->setPaymentgroup($this);
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

    /**
     * Get Agency
     *
     * @return Agency
     */
    public function getAgency() {
        return $this->agency;
    }

    /**
     * Set Agency
     *
     * @param Agency $agency
     * @return RealtorGroup
     */
    public function setAgency(Agency $agency) {
        $this->agency = $agency;

        return $this;
    }
    
    public function isPossitionAssigned($position) {
        $realtors = $this->realtors;
        foreach($realtors as $c) {
            if ($c->getGroupposition() === $position) {
                return true;
            }
        }
        return false;
    }
    
    public function getPayPendingCount($date) {
        $count = 0;
        $realtors = $this->realtors;
        foreach($realtors as $c) {
            $count = $count + $c->getUnpaidChargesCount($date);
        }
        return $count;
    }
}
