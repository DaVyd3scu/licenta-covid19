<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\ModernaVaccine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ModernaVaccineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModernaVaccine::class);
    }
}
