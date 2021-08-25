<?php

namespace App\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Cases\ActiveCasesRepository")
 * @ORM\Table(name="active_cases")
 */
class ActiveCases extends Cases
{

}
