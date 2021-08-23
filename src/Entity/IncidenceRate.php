<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="incidence_rate")
 */
class IncidenceRate
{
    /**
     * @var DateTime
     *
     * @ORM\Id()
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var County
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\County", inversedBy="incidenceRate")
     * @ORM\JoinColumn(name="county_code", referencedColumnName="code")
     */
    private $county;

    /**
     * @var int
     *
     * @ORM\Column(name="incidence_rate", type="integer")
     */
    private $incidenceRate;
}
