<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\PfizerVaccine;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PfizerVaccineRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PfizerVaccine::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('pv')
            ->from('App:Vaccine\PfizerVaccine', 'pv')
            ->where('pv.date >= :startingPeriod AND pv.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
