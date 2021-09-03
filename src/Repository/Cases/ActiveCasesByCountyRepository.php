<?php

namespace App\Repository\Cases;

use App\Entity\Cases\ActiveCasesByCounty;
use App\Entity\County;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ActiveCasesByCountyRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiveCasesByCounty::class);

        $this->queryBuilder = $this->_em->createQueryBuilder();
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

    /**
     * @param DateTime|null $startingPeriod
     * @param DateTime|null $endingPeriod
     * @param County|null $county
     *
     * @return int|array|string
     */
    public function findActiveCasesByFilters(
        County $county,
        DateTime $startingPeriod = null,
        DateTime $endingPeriod = null
    ) {
        return $this->queryBuilder->select('acc')
            ->from('App:Cases\ActiveCasesByCounty', 'acc')
            ->where('acc.county = :county AND acc.date >= :startingPeriod AND acc.date <= :endingPeriod')
            ->setParameter('county', $county)
            ->setParameter('startingPeriod', $startingPeriod)
            ->setParameter('endingPeriod', $endingPeriod)
            ->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getTotalActiveCasesByCountyArray(): array
    {
        $results = $this->findBy([], ['date' => 'DESC'], 42);
        $dataToBeReturned = [];

        foreach ($results as $result) {
            $code = $result->getCounty()->getCode();
            $name = $result->getCounty()->getName();
            $totalCases = $result->getCurrentDayNumber();

            $dataToBeReturned[$code] = [$name => $totalCases];
        }

        return $dataToBeReturned;
    }
}
