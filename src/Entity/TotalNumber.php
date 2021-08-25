<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\TotalNumberRepository")
 * @ORM\Table(name="total_numbers")
 */
class TotalNumber
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
     * @ORM\Column(name="total_cases", type="integer")
     */
    private $totalCases;

    /**
     * @var int
     *
     * @ORM\Column(name="doses_of_vaccine_administered", type="integer", nullable=true)
     */
    private $dosesOfVaccineAdministered;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return TotalNumber
     */
    public function setDate(DateTime $date): TotalNumber
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCases(): int
    {
        return $this->totalCases;
    }

    /**
     * @param int $totalCases
     *
     * @return TotalNumber
     */
    public function setTotalCases(int $totalCases): TotalNumber
    {
        $this->totalCases = $totalCases;

        return $this;
    }

    /**
     * @return int
     */
    public function getDosesOfVaccineAdministered(): int
    {
        return $this->dosesOfVaccineAdministered;
    }

    /**
     * @param int $dosesOfVaccineAdministered
     *
     * @return TotalNumber
     */
    public function setDosesOfVaccineAdministered(int $dosesOfVaccineAdministered): TotalNumber
    {
        $this->dosesOfVaccineAdministered = $dosesOfVaccineAdministered;

        return $this;
    }
}
