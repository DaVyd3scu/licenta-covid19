<?php

namespace App\Repository\Cases;

use App\Entity\Cases\ActiveCasesByCounty;
use App\Entity\County;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActiveCasesByCountyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveCasesByCounty::class);
    }

    /**
     * @param County $county
     * @param DateTime $date
     *
     * @return int
     */
    public function findInfectionsNumberByCountyAndDate(County $county, DateTime $date): int
    {
        return $this->findOneBy([
            'date' => $date,
            'county' => $county
        ])->getCurrentDayNumber();
    }
}
