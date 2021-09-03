<?php

namespace App\Repository\Cases;

use App\Entity\Cases\HealedCases;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HealedCasesRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealedCases::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('hc')
            ->from('App:Cases\HealedCases', 'hc')
            ->where('hc.date >= :startingPeriod AND hc.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
