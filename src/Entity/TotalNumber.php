<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="total_numbers")
 */
class TotalNumber
{
    /**
     * @var DateTime
     *
     * @ORM\Id()
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
     * @ORM\Column(name="doses_of_vaccine_administered", type="integer")
     */
    private $dosesOfVaccineAdministered;
}
