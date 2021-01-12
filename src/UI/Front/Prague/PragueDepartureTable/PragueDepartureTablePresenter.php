<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueDepartureTable\Control\FrontPragueDepartureTable\FrontPragueDepartureTableControl;
use App\UI\Front\Prague\PragueDepartureTable\Control\FrontPragueDepartureTable\FrontPragueDepartureTableControlFactory;
use Ramsey\Uuid\Uuid;

class PragueDepartureTablePresenter extends FrontPresenter
{
	private FrontPragueDepartureTableControlFactory $departureTableControlFactory;

	private DepartureTableRepository $departureTableRepository;

	public function __construct(
		FrontPragueDepartureTableControlFactory $departureTableControlFactory,
		DepartureTableRepository $departureTableRepository
	) {
		parent::__construct();
		$this->departureTableControlFactory = $departureTableControlFactory;
		$this->departureTableRepository = $departureTableRepository;
	}

	public function renderDetail(string $id): void
	{
		$this->getTemplate()->departureTable = $this->departureTableRepository->findById(Uuid::fromString($id));
	}

	protected function createComponentDepartureTableControl(): FrontPragueDepartureTableControl
	{
		return $this->departureTableControlFactory->create($this->processParameterStringId());
	}
}
