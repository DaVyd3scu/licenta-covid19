<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\JohnsonAndJohnsonVaccine;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JohnsonAndJohnsonVaccineRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JohnsonAndJohnsonVaccine::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('jjv')
            ->from('App:Vaccine\JohnsonAndJohnsonVaccine', 'jjv')
            ->where('jjv.date >= :startingPeriod AND jjv.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
