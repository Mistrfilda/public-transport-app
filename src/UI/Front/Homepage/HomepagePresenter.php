<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControl;
use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControlFactory;
use App\UI\Admin\PragueDepartureTable\Exception\InvalidArgumentException;
use App\UI\Front\FrontPresenter;

class HomepagePresenter extends FrontPresenter
{
    /** @var DepartureTableControlFactory */
    private $departureTableControlFactory;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    public function __construct(
        DepartureTableControlFactory $departureTableControlFactory,
        DepartureTableRepository $departureTableRepository
    ) {
        parent::__construct();
        $this->departureTableControlFactory = $departureTableControlFactory;
        $this->departureTableRepository = $departureTableRepository;
    }

    public function renderDefault(): void
    {
        $this->template->departureTables = $this->departureTableRepository->findAll();
    }

    public function renderDepartureTable(string $id): void
    {
    }

    protected function createComponentDepartureTableControl(): DepartureTableControl
    {
        $id = $this->getParameter('id');
        if ($id === null) {
            throw new InvalidArgumentException('Missing parameter ID');
        }

        return $this->departureTableControlFactory->create($id);
    }
}
