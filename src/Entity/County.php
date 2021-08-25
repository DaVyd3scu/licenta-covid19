<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\CountyRepository")
 * @ORM\Table(name="counties")
 */
class County
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\Entity\IncidenceRate", mappedBy="county")
     */
    private $incidenceRates;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Cases\ActiveCases", mappedBy="county")
     */
    private $activeCases;

    public function __construct()
    {
        $this->incidenceRates = new ArrayCollection();
        $this->activeCases = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncidenceRates(): ArrayCollection
    {
        return $this->incidenceRates;
    }

    /**
     * @return ArrayCollection
     */
    public function getActiveCases(): ArrayCollection
    {
        return $this->activeCases;
    }
}
