<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\JohnsonAndJohnsonVaccine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JohnsonAndJohnsonVaccineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JohnsonAndJohnsonVaccine::class);
    }
}
