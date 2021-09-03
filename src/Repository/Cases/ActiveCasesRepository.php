<?php

namespace App\Repository\Cases;

use App\Entity\Cases\ActiveCases;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActiveCasesRepository extends ServiceEntityRepository
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveCases::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('ac')
            ->from('App:Cases\ActiveCases', 'ac')
            ->where('ac.date >= :startingPeriod AND ac.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
