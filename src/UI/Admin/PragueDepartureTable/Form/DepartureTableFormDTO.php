<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Form;

use Nette\SmartObject;

class DepartureTableFormDTO
{
    use SmartObject;

    /** @var int */
    public $stopId;

    /** @var int */
    public $numberOfFutureDays;

    public function getStopId(): int
    {
        return $this->stopId;
    }

    public function getNumberOfFutureDays(): int
    {
        return $this->numberOfFutureDays;
    }
}
