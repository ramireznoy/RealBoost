<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settlement
 *
 * @ORM\Table(name="core_settlements")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\SettlementRepository")
 */
class Settlement
{
    /**
     * @var bigint
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="advisor", type="string", length=255)
     */
    private $advisor;

    /**
     * @var string
     *
     * @ORM\Column(name="coordinator", type="string", length=255)
     */
    private $coordinator;

    /**
     * @var string
     *
     * @ORM\Column(name="groupId", type="string", length=255)
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="groupName", type="string", length=255)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="groupLoan", type="decimal", precision=10, scale=2)
     */
    private $groupLoan;

    /**
     * @var string
     *
     * @ORM\Column(name="groupFee", type="decimal", precision=10, scale=2)
     */
    private $groupFee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reunionDate", type="date")
     */
    private $reunionDate;

    /**
     * @var \Time
     *
     * @ORM\Column(name="reunionTime", type="time")
     */
    private $reunionTime;

    /**
     * @var string
     *
     * @ORM\Column(name="groupHome", type="string", length=255)
     */
    private $groupHome;

    /**
     * @var int
     *
     * @ORM\Column(name="clientCount", type="integer")
     */
    private $clientCount;

    /**
     * @var int
     *
     * @ORM\Column(name="term", type="integer")
     */
    private $term;

    /**
     * @var string
     *
     * @ORM\Column(name="clientId", type="string", length=255)
     */
    private $clientId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="clientName", type="string", length=255)
     */
    private $clientName;

    /**
     * @var string
     *
     * @ORM\Column(name="clientPosition", type="string", length=255)
     */
    private $clientPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="clientHome", type="string", length=255)
     */
    private $clientHome;

    /**
     * @var string
     *
     * @ORM\Column(name="clientPhone", type="string", length=255)
     */
    private $clientPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="clientWeekspayed", type="integer")
     */
    private $clientWeekspayed;

    /**
     * @var int
     *
     * @ORM\Column(name="clientWeekstopay", type="integer")
     */
    private $clientWeekstopay;

    /**
     * @var string
     *
     * @ORM\Column(name="clientLoan", type="decimal", precision=10, scale=2)
     */
    private $clientLoan;

    /**
     * @var string
     *
     * @ORM\Column(name="clientDebt", type="decimal", precision=10, scale=2)
     */
    private $clientDebt;

    /**
     * @var string
     *
     * @ORM\Column(name="clientFee", type="decimal", precision=10, scale=2)
     */
    private $clientFee;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="settled", type="boolean", options={"default" : false})
     */
    private $settled;
    
    /**
     * @var string
     *
     * @ORM\Column(name="settledFor", type="decimal", precision=10, scale=2)
     */
    private $settledFor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="confirmation", type="string", length=255)
     */
    private $confirmation;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="settledAt", type="datetime")
     */
    private $settledAt;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentForm", type="string", length=255)
     */
    private $paymentform;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ticketpath", type="string", length=255)
     */
    private $ticketpath;

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
     * Set advisor
     *
     * @param string $advisor
     *
     * @return Settlement
     */
    public function setAdvisor($advisor)
    {
        $this->advisor = $advisor;

        return $this;
    }

    /**
     * Get advisor
     *
     * @return string
     */
    public function getAdvisor()
    {
        return $this->advisor;
    }

    /**
     * Set coordinator
     *
     * @param string $coordinator
     *
     * @return Settlement
     */
    public function setCoordinator($coordinator)
    {
        $this->coordinator = $coordinator;

        return $this;
    }

    /**
     * Get coordinator
     *
     * @return string
     */
    public function getCoordinator()
    {
        return $this->coordinator;
    }

    /**
     * Set groupId
     *
     * @param string $groupId
     *
     * @return Settlement
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set groupName
     *
     * @param string $groupName
     *
     * @return Settlement
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set groupLoan
     *
     * @param string $groupLoan
     *
     * @return Settlement
     */
    public function setGroupLoan($groupLoan)
    {
        $this->groupLoan = $groupLoan;

        return $this;
    }

    /**
     * Get groupLoan
     *
     * @return string
     */
    public function getGroupLoan()
    {
        return $this->groupLoan;
    }

    /**
     * Set groupFee
     *
     * @param string $groupFee
     *
     * @return Settlement
     */
    public function setGroupFee($groupFee)
    {
        $this->groupFee = $groupFee;

        return $this;
    }

    /**
     * Get groupFee
     *
     * @return string
     */
    public function getGroupFee()
    {
        return $this->groupFee;
    }

    /**
     * Set reunionDate
     *
     * @param \DateTime $reunionDate
     *
     * @return Settlement
     */
    public function setReunionDate($reunionDate)
    {
        $this->reunionDate = $reunionDate;

        return $this;
    }

    /**
     * Get reunionDate
     *
     * @return \DateTime
     */
    public function getReunionDate()
    {
        return $this->reunionDate;
    }

    /**
     * Set reunionTime
     *
     * @param \DateTime $reunionTime
     *
     * @return Settlement
     */
    public function setReunionTime($reunionTime)
    {
        $this->reunionTime = $reunionTime;

        return $this;
    }

    /**
     * Get reunionTime
     *
     * @return \DateTime
     */
    public function getReunionTime()
    {
        return $this->reunionTime;
    }

    /**
     * Set groupHome
     *
     * @param string $groupHome
     *
     * @return Settlement
     */
    public function setGroupHome($groupHome)
    {
        $this->groupHome = $groupHome;

        return $this;
    }

    /**
     * Get groupHome
     *
     * @return string
     */
    public function getGroupHome()
    {
        return $this->groupHome;
    }

    /**
     * Set clientCount
     *
     * @param integer $clientCount
     *
     * @return Settlement
     */
    public function setClientCount($clientCount)
    {
        $this->clientCount = $clientCount;

        return $this;
    }

    /**
     * Get clientCount
     *
     * @return int
     */
    public function getClientCount()
    {
        return $this->clientCount;
    }

    /**
     * Set term
     *
     * @param integer $term
     *
     * @return Settlement
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return int
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set clientId
     *
     * @param string $clientId
     *
     * @return Settlement
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }
    
    /**
     * Set client name
     *
     * @param string $clientName
     *
     * @return Settlement
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get client name
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set clientPosition
     *
     * @param string $clientPosition
     *
     * @return Settlement
     */
    public function setClientPosition($clientPosition)
    {
        $this->clientPosition = $clientPosition;

        return $this;
    }

    /**
     * Get clientPosition
     *
     * @return string
     */
    public function getClientPosition()
    {
        return $this->clientPosition;
    }

    /**
     * Set clientHome
     *
     * @param string $clientHome
     *
     * @return Settlement
     */
    public function setClientHome($clientHome)
    {
        $this->clientHome = $clientHome;

        return $this;
    }

    /**
     * Get clientHome
     *
     * @return string
     */
    public function getClientHome()
    {
        return $this->clientHome;
    }

    /**
     * Set clientPhone
     *
     * @param string $clientPhone
     *
     * @return Settlement
     */
    public function setClientPhone($clientPhone)
    {
        $this->clientPhone = $clientPhone;

        return $this;
    }

    /**
     * Get clientPhone
     *
     * @return string
     */
    public function getClientPhone()
    {
        return $this->clientPhone;
    }

    /**
     * Set clientWeekspayed
     *
     * @param integer $clientWeekspayed
     *
     * @return Settlement
     */
    public function setClientWeekspayed($clientWeekspayed)
    {
        $this->clientWeekspayed = $clientWeekspayed;

        return $this;
    }

    /**
     * Get clientWeekspayed
     *
     * @return int
     */
    public function getClientWeekspayed()
    {
        return $this->clientWeekspayed;
    }

    /**
     * Set clientWeekstopay
     *
     * @param integer $clientWeekstopay
     *
     * @return Settlement
     */
    public function setClientWeekstopay($clientWeekstopay)
    {
        $this->clientWeekstopay = $clientWeekstopay;

        return $this;
    }

    /**
     * Get clientWeekstopay
     *
     * @return int
     */
    public function getClientWeekstopay()
    {
        return $this->clientWeekstopay;
    }

    /**
     * Set clientLoan
     *
     * @param string $clientLoan
     *
     * @return Settlement
     */
    public function setClientLoan($clientLoan)
    {
        $this->clientLoan = $clientLoan;

        return $this;
    }

    /**
     * Get clientLoan
     *
     * @return string
     */
    public function getClientLoan()
    {
        return $this->clientLoan;
    }

    /**
     * Set clientDebt
     *
     * @param string $clientDebt
     *
     * @return Settlement
     */
    public function setClientDebt($clientDebt)
    {
        $this->clientDebt = $clientDebt;

        return $this;
    }

    /**
     * Get clientDebt
     *
     * @return string
     */
    public function getClientDebt()
    {
        return $this->clientDebt;
    }

    /**
     * Set clientFee
     *
     * @param string $clientFee
     *
     * @return Settlement
     */
    public function setClientFee($clientFee)
    {
        $this->clientFee = $clientFee;

        return $this;
    }

    /**
     * Get clientFee
     *
     * @return string
     */
    public function getClientFee()
    {
        return $this->clientFee;
    }
    
    public function isSettled() {
        return $this->settled;
    }

    public function getSettledFor() {
        return $this->settledFor;
    }

    public function getConfirmation() {
        return $this->confirmation;
    }

    public function getSettledAt() {
        return $this->settledAt;
    }

    public function setSettled($settled) {
        $this->settled = $settled;
        return $this;
    }

    public function setSettledFor($settledFor) {
        $this->settledFor = $settledFor;
        return $this;
    }

    public function setConfirmation($confirmation) {
        $this->confirmation = $confirmation;
        return $this;
    }

    public function setSettledAt(\DateTime $settledAt) {
        $this->settledAt = $settledAt;
        return $this;
    }
    
    public function getPaymentform() {
        return $this->paymentform;
    }

    public function setPaymentform($paymentform) {
        $this->paymentform = $paymentform;
        return $this;
    }
    
    public function getTicketpath() {
        return $this->ticketpath;
    }

    public function setTicketpath($ticketpath) {
        $this->ticketpath = $ticketpath;
        return $this;
    }
}

