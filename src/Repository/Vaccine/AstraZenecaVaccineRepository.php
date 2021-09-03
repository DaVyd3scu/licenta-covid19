<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\AstraZenecaVaccine;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AstraZenecaVaccineRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AstraZenecaVaccine::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('azv')
            ->from('App:Vaccine\AstraZenecaVaccine', 'azv')
            ->where('azv.date >= :startingPeriod AND azv.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
