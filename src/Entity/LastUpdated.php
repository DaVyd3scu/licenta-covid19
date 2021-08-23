<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="last_updated")
 */
class LastUpdated
{
    /**
     * @var DateTime
     *
     * @ORM\Id()
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $lastUpdate;

    /**
     * @return DateTime
     */
    public function getLastUpdate(): DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param DateTime $lastUpdate
     *
     * @return LastUpdated
     */
    public function setLastUpdate(DateTime $lastUpdate): LastUpdated
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
