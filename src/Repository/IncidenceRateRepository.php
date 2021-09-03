<?php

namespace App\Repository;

use App\Entity\IncidenceRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IncidenceRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncidenceRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncidenceRate[]    findAll()
 * @method IncidenceRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncidenceRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncidenceRate::class);
    }

    /**
     * @return array
     */
    public function getIncidenceArray(): array
    {
        $results = $this->findAll();
        $dataToBeReturned = [];

        foreach ($results as $result) {
            $code = $result->getCounty()->getCode();
            $name = $result->getCounty()->getName();
            $incidenceRate = $result->getIncidenceRate();

            $dataToBeReturned[$code] = [$name => $incidenceRate];
        }

        return $dataToBeReturned;
    }
}
