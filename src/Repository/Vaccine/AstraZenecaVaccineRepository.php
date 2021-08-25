<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\AstraZenecaVaccine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AstraZenecaVaccineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AstraZenecaVaccine::class);
    }
}
