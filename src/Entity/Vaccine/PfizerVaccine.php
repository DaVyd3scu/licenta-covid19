<?php

namespace App\Entity\Vaccine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Vaccine\PfizerVaccineRepository")
 * @ORM\Table(name="pfizer_vaccine")
 */
class PfizerVaccine extends Vaccines
{

}
