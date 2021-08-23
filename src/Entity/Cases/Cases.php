<?php

namespace App\Entity\Cases;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class Cases
{
    /**
     * @var DateTime
     *
     * @ORM\Id
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="current_day_number", type="integer")
     */
    private $currentDayNumber;

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
    public function getCurrentDayNumber(): int
    {
        return $this->currentDayNumber;
    }

    /**
     * @param mixed $currentDayNumber
     *
     * @return $this
     */
    public function setCurrentDayNumber($currentDayNumber): self
    {
        $this->currentDayNumber = $currentDayNumber;

        return $this;
    }
}
