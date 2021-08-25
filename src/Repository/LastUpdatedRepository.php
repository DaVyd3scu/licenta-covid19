<?php

namespace App\Repository;

use App\Entity\LastUpdated;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LastUpdatedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastUpdated::class);
    }

    /**
     * @return DateTime|null
     */
    public function getLastUpdateDateTime(): ?DateTime
    {
        $result = $this->findOneBy(['id' => 1]);

        if ($result) {
            return $result->getLastUpdate();
        }

        return null;
    }

    /**
     * @return LastUpdated|object|null
     */
    public function getLastUpdateObject()
    {
        return $this->findOneBy(['id' => 1]);
    }
}
