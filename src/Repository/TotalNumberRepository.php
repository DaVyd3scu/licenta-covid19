<?php

namespace App\Repository;

use App\Entity\TotalNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TotalNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method TotalNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method TotalNumber[]    findAll()
 * @method TotalNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TotalNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TotalNumber::class);
    }
}
