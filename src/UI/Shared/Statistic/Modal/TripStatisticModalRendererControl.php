<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Modal;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Shared\Modal\ModalRendererControl;

class TripStatisticModalRendererControl extends ModalRendererControl
{
    /** @var TripStatisticDataRepository */
    private $tripStatisticDataRepository;

    /** @var string */
    private $tripId;

    public function __construct(string $tripId, TripStatisticDataRepository $tripStatisticDataRepository)
    {
        parent::__construct();
        $this->tripStatisticDataRepository = $tripStatisticDataRepository;
        $this->tripId = $tripId;
    }

    public function render(): void
    {
        $this->templateFile = __DIR__ . '/modal.latte';
        $this->getTemplate()->tripId = $this->tripId;
        $this->getTemplate()->tripStatistics = $this->tripStatisticDataRepository->findByTripId($this->tripId);
        parent::render();
    }
}
