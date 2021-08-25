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
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var County
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\County", inversedBy="incidenceRate")
     * @ORM\JoinColumn(name="county_code", referencedColumnName="code")
     */
    private $county;

    /**
     * @var float
     *
     * @ORM\Column(name="incidence_rate", type="float")
     */
    private $incidenceRate;

    /**
     * @return County
     */
    public function getCounty(): County
    {
        return $this->county;
    }

    /**
     * @param County $county
     *
     * @return IncidenceRate
     */
    public function setCounty(County $county): IncidenceRate
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return float
     */
    public function getIncidenceRate(): float
    {
        return $this->incidenceRate;
    }

    /**
     * @param float $incidenceRate
     *
     * @return IncidenceRate
     */
    public function setIncidenceRate(float $incidenceRate): IncidenceRate
    {
        $this->incidenceRate = $incidenceRate;

        return $this;
    }
}
