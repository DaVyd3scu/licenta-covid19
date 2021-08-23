<?php

namespace App\Entity\Cases;

use App\Entity\County;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="active_cases")
 */
class ActiveCases extends Cases
{
    /**
     * @var County
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\County", inversedBy="activeCases")
     * @ORM\JoinColumn(name="county_code", referencedColumnName="code", nullable=true)
     */
    private $county;
}
