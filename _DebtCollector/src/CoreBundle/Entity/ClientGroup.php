<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ClientGroup
 *
 * @ORM\Table(name="core_clientgroups")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\ClientGroupRepository")
 */
class ClientGroup {

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
     * @var \Doctrine\Common\Collections\Collection|Client[]
     *
     * One ClientGroup has Many Clients.
     * @ORM\OneToMany(targetEntity="Client", mappedBy="paymentgroup", cascade={"persist", "remove"})
     */
    private $clients;

    /**
     * @var Advisor
     *
     * @ORM\ManyToOne(targetEntity="Advisor", inversedBy="clientgroups")
     * @ORM\JoinColumn(name="advisor", referencedColumnName="id")
     */
    private $advisor;

    /**
     * Constructor for ClientGroup
     */
    public function __construct() {
        $this->clients = new ArrayCollection();
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
     * @return ClientGroup
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
     * @return ClientGroup
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
     * @return ClientGroup
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
     * @return ClientGroup
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
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection|Client[]
     */
    public function getClients() {
        return $this->clients;
    }

    /**
     * @param Client $client
     */
    public function addClient(Client $client) {
        if ($this->clients->contains($client)) {
            return;
        }
        $client->setPaymentgroup($this);
        $this->clients->add($client);
    }

    /**
     * @param Client $client
     */
    public function removeClient(Client $client) {
        if (!$this->clients->contains($client)) {
            return;
        }
        $this->clients->removeElement($client);
    }

    /**
     * Get Advisor
     *
     * @return Advisor
     */
    public function getAdvisor() {
        return $this->advisor;
    }

    /**
     * Set Advisor
     *
     * @param Advisor $advisor
     * @return ClientGroup
     */
    public function setAdvisor(Advisor $advisor) {
        $this->advisor = $advisor;

        return $this;
    }
    
    public function isPossitionAssigned($position) {
        $clients = $this->clients;
        foreach($clients as $c) {
            if ($c->getGroupposition() === $position) {
                return true;
            }
        }
        return false;
    }
    
    public function getPayPendingCount($date) {
        $count = 0;
        $clients = $this->clients;
        foreach($clients as $c) {
            $count = $count + $c->getUnpaidChargesCount($date);
        }
        return $count;
    }
}
