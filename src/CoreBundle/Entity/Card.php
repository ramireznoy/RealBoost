<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Card
 *
 * @ORM\Table(name="card")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CardRepository")
 */
class Card
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
     * @ORM\Column(name="contact_name", type="string", length=255)
     */
    private $contactName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_mail", type="string", length=255)
     */
    private $contactMail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_picture", type="string", length=255, nullable=true)
     */
    private $contactPicture;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_phone", type="string", length=255)
     */
    private $contactPhone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=255)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="business_logo", type="string", length=255, nullable=true)
     */
    private $businessLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="business_phone", type="string", length=255, nullable=true)
     */
    private $businessPhone;
    
    /**
     * The templates can be used in many cards.
     * 
     * @ORM\ManyToOne(targetEntity="CardTemplate")
     * @ORM\JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;
    
    /**
     * @var \Doctrine\Common\Collections\Collection|Contact[]
     *
     * @ORM\ManyToMany(targetEntity="Contact", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="relation_card_contacts",
     *      joinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $contacts;
    
    
    /**
     * Constructor for Card
     */
    public function __construct() {
        $this->contacts = new ArrayCollection();
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
     * Set contactName
     *
     * @param string $contactName
     *
     * @return Card
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set contactMail
     *
     * @param string $contactMail
     *
     * @return Card
     */
    public function setContactMail($contactMail)
    {
        $this->contactMail = $contactMail;

        return $this;
    }

    /**
     * Get contactMail
     *
     * @return string
     */
    public function getContactMail()
    {
        return $this->contactMail;
    }

    /**
     * Set contactPicture
     *
     * @param string $contactPicture
     *
     * @return Card
     */
    public function setContactPicture($contactPicture)
    {
        $this->contactPicture = $contactPicture;

        return $this;
    }

    /**
     * Get contactPicture
     *
     * @return string
     */
    public function getContactPicture()
    {
        return $this->contactPicture;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     *
     * @return Card
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessLogo
     *
     * @param string $businessLogo
     *
     * @return Card
     */
    public function setBusinessLogo($businessLogo)
    {
        $this->businessLogo = $businessLogo;

        return $this;
    }

    /**
     * Get businessLogo
     *
     * @return string
     */
    public function getBusinessLogo()
    {
        return $this->businessLogo;
    }

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     *
     * @return Card
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * Get contactPhone
     *
     * @return string
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set businessPhone
     *
     * @param string $businessPhone
     *
     * @return Card
     */
    public function setBusinessPhone($businessPhone)
    {
        $this->businessPhone = $businessPhone;

        return $this;
    }

    /**
     * Get businessPhone
     *
     * @return string
     */
    public function getBusinessPhone()
    {
        return $this->businessPhone;
    }
}

