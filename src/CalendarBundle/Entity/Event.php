<?php

namespace CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SettingsRepository")
 */
class Event
{
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


    /**
     * @var datetime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;
    
     /**
     * One Event has Many Schedules.
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="event")
     */
    private $schedule;
    
     /**
     * @var bool
     *
     * @ORM\Column(name="sms_notification", type="boolean", options={"default" : false})
     */
    private $sms_notification;
    
    
    
        /**
     * @var bool
     *
     * @ORM\Column(name="email_notification", type="boolean", options={"default" : false})
     */
    private $email_notification;
    
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
     * Constructor
     */
    public function __construct() {
        $this->schedule = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Settings
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string description
     *
     * @return description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Event
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set smsNotification
     *
     * @param boolean $smsNotification
     *
     * @return Event
     */
    public function setSmsNotification($smsNotification)
    {
        $this->sms_notification = $smsNotification;

        return $this;
    }

    /**
     * Get smsNotification
     *
     * @return boolean
     */
    public function getSmsNotification()
    {
        return $this->sms_notification;
    }

    /**
     * Set emailNotification
     *
     * @param boolean $emailNotification
     *
     * @return Event
     */
    public function setEmailNotification($emailNotification)
    {
        $this->email_notification = $emailNotification;

        return $this;
    }

    /**
     * Get emailNotification
     *
     * @return boolean
     */
    public function getEmailNotification()
    {
        return $this->email_notification;
    }

    /**
     * Add schedule
     *
     * @param \CalendarBundle\Entity\Schedule $schedule
     *
     * @return Event
     */
    public function addSchedule(\CalendarBundle\Entity\Schedule $schedule)
    {
        $this->schedule[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \CalendarBundle\Entity\Schedule $schedule
     */
    public function removeSchedule(\CalendarBundle\Entity\Schedule $schedule)
    {
        $this->schedule->removeElement($schedule);
    }

    /**
     * Get schedule
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
