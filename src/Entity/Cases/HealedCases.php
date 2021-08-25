<?php

namespace App\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Cases\HealedCasesRepository")
 * @ORM\Table(name="healed_cases")
 */
class HealedCases extends Cases
{

}
