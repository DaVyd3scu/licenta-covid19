<?php

namespace App\Entity\Cases;

use App\Entity\County;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Cases\ActiveCasesByCountyRepository")
 * @ORM\Table(name="active_cases_by_county")
 */
class ActiveCasesByCounty extends Cases
{
    /**
     * @var County
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\County", inversedBy="activeCases")
     * @ORM\JoinColumn(name="county_code", referencedColumnName="code", nullable=true)
     */
    private $county;

    /**
     * @var int
     *
     * @ORM\Column(name="new_cases", type="integer", nullable=true)
     */
    private $newCases;

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
     * @return ActiveCasesByCounty
     */
    public function setCounty(County $county): ActiveCasesByCounty
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewCases(): int
    {
        return $this->newCases;
    }

    /**
     * @param int $newCases
     *
     * @return ActiveCasesByCounty
     */
    public function setNewCases(int $newCases): ActiveCasesByCounty
    {
        $this->newCases = $newCases;

        return $this;
    }


}
