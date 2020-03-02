<?php

declare(strict_types=1);

namespace App\UI\Front\PragueDepartureTable;

use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControl;
use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControlFactory;
use App\UI\Admin\PragueDepartureTable\Exception\InvalidArgumentException;
use App\UI\Front\FrontPresenter;

class PragueDepartureTablePresenter extends FrontPresenter
{
    /** @var DepartureTableControlFactory */
    private $departureTableControlFactory;

    public function __construct(
        DepartureTableControlFactory $departureTableControlFactory
    ) {
        parent::__construct();
        $this->departureTableControlFactory = $departureTableControlFactory;
    }

    public function renderDetail(string $id): void
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
