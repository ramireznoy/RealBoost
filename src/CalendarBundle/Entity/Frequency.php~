<?php

namespace CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Frequency
 *
 * @ORM\Table(name="frequency")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\FrequencyRepository")
 */
class Frequency
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
     * @ORM\Column(name="name", type="integer")
     */
    private $frequency;
    
    
    /** @ORM\Column(name="offset", type="integer")
     */
    private $offset;

     /**
     * One Frequency has Many Schedules.
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="frequency")
     */
    private $schedule;
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
     * Set name
     *
     * @param string $name
     *
     * @return Settings
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }


    /**
     * Set offset
     *
     * @param integer $offset
     *
     * @return Frequency
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedule = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add schedule
     *
     * @param \CalendarBundle\Entity\Schedule $schedule
     *
     * @return Frequency
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
