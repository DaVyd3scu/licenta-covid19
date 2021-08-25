<?php

namespace App\Entity\Vaccine;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class Vaccines
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="current_day_number_of_doses", type="integer")
     */
    private $currentDayNumberOfDoses;

    /**
     * @var int
     *
     * @ORM\Column(name="people_immunized", type="integer")
     */
    private $peopleImmunized;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     *
     * @return $this
     */
    public function setDate($date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentDayNumberOfDoses(): int
    {
        return $this->currentDayNumberOfDoses;
    }

    /**
     * @param mixed $currentDayNumberOfDoses
     *
     * @return $this
     */
    public function setCurrentDayNumberOfDoses($currentDayNumberOfDoses): self
    {
        $this->currentDayNumberOfDoses = $currentDayNumberOfDoses;

        return $this;
    }

    /**
     * @return int
     */
    public function getPeopleImmunized(): int
    {
        return $this->peopleImmunized;
    }

    /**
     * @param mixed $peopleImmunized
     *
     * @return $this
     */
    public function setPeopleImmunized($peopleImmunized): self
    {
        $this->peopleImmunized = $peopleImmunized;

        return $this;
    }
}
