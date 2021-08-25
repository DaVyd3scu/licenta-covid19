<?php

namespace App\Repository\Cases;

use App\Entity\Cases\ActiveCasesByCounty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActiveCasesByCountyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveCasesByCounty::class);
    }
}
