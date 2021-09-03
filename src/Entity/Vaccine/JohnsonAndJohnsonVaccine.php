<?php

namespace App\Entity\Vaccine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Vaccine\JohnsonAndJohnsonVaccineRepository")
 * @ORM\Table(name="johnson_johnson_vaccine")
 */
class JohnsonAndJohnsonVaccine extends Vaccines
{

}
