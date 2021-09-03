<?php

namespace App\Repository\Vaccine;

use App\Entity\Vaccine\ModernaVaccine;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ModernaVaccineRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModernaVaccine::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function findByFilters(DateTime $startingPeriod, DateTime $endingPeriod)
    {
        return $this->queryBuilder->select('mv')
            ->from('App:Vaccine\ModernaVaccine', 'mv')
            ->where('mv.date >= :startingPeriod AND mv.date <= :endingPeriod')
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }
}
