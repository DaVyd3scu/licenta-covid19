<?php

namespace App\Entity\Vaccine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Vaccine\AstraZenecaVaccineRepository")
 * @ORM\Table(name="astra_zeneca_vaccine")
 */
class AstraZenecaVaccine extends Vaccines
{

}
