<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Doctrine\IEntity;
use App\Doctrine\Uuid;
use App\Transport\DepartureTable\IDepartureTable;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Stop\IStop;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_departure_table")
 */
class DepartureTable implements IDepartureTable, IEntity
{
    use Uuid;

    /**
     * @var Stop
     * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
     */
    private $stop;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $numberOfFutureDays;

    public function __construct(Stop $stop, int $numberOfFutureDays)
    {
        $this->stop = $stop;
        $this->numberOfFutureDays = $numberOfFutureDays;
    }

    public function update(int $numberOfFutureDays): void
    {
        $this->numberOfFutureDays = $numberOfFutureDays;
    }

    public function getStop(): IStop
    {
        return $this->stop;
    }

    public function getDownloadNumberOfDays(): int
    {
        return $this->numberOfFutureDays;
    }
}
