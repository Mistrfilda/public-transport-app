<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Front\FrontPresenter;

class HomepagePresenter extends FrontPresenter
{
    /** @var DepartureTableRepository */
    private $departureTableRepository;

    public function __construct(
        DepartureTableRepository $departureTableRepository
    ) {
        parent::__construct();
        $this->departureTableRepository = $departureTableRepository;
    }

    public function renderDefault(): void
    {
        $this->template->departureTables = $this->departureTableRepository->findAll();
    }
}
