<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\FrontPragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopLineFactory;
use App\UI\Admin\Control\Statistic\Modal\TripStatisticModalRendererControl;
use App\UI\Admin\Control\Statistic\Modal\TripStatisticModalRendererControlFactory;
use App\UI\Front\Base\BaseControl;
use App\UI\Shared\LogicException;
use Nette\Http\Session;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class FrontPragueDepartureTableControl extends BaseControl
{
	private UuidInterface $id;

	private ?string $tripId = null;

	private bool $renderModal = false;

	private DepartureTableRepository $departureTableRepository;

	private StopLineFactory $stopLineFactory;

	private FrontPragueDepartureTablePaginatorService $paginatorService;

	private TripStatisticModalRendererControlFactory $tripStatisticModalRendererControlFactory;

	public function __construct(
		string $id,
		DepartureTableRepository $departureTableRepository,
		StopLineFactory $stopLineFactory,
		Session $session,
		TripStatisticModalRendererControlFactory $tripStatisticModalRendererControlFactory
	) {
		$this->id = Uuid::fromString($id);
		$this->departureTableRepository = $departureTableRepository;
		$this->stopLineFactory = $stopLineFactory;
		$this->paginatorService = new FrontPragueDepartureTablePaginatorService($this->id, $session);
		$this->tripStatisticModalRendererControlFactory = $tripStatisticModalRendererControlFactory;
	}

	public function render(): void
	{
		$template = $this->createTemplate();
		$departureTable = $this->departureTableRepository->findById($this->id);
		$stopLines = $this->stopLineFactory->getStopLinesForStop(
			$departureTable->getPragueStop(),
			$this->paginatorService->getLoadedCount()
		);

		$showLoadMoreButton = true;
		if ($this->paginatorService->getLoadedCount() > count($stopLines)) {
			$showLoadMoreButton = false;
		}

		$template->renderModal = $this->renderModal;
		$template->stopLines = $stopLines;
		$template->departureTable = $departureTable;
		$template->currentStep = $this->paginatorService->getCurrentStep();
		$template->showLoadMoreButton = $showLoadMoreButton;
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
	}

	public function handleRefresh(): void
	{
		$this->redrawTable();
	}

	public function handleLoadMore(): void
	{
		$this->paginatorService->increase();
		$this->redrawTable();
	}

	public function handleResetPagination(): void
	{
		$this->paginatorService->reset();
		$this->redrawTable();
	}

	public function handleTripIdStatistics(string $tripId): void
	{
		$this->tripId = $tripId;
		$modalComponent = $this->getComponent('tripStatisticModalControl');
		$this->renderModal = true;
		$this->presenter->payload->showModal = true;
		$this->presenter->payload->modalId = $modalComponent->getModalId();
		$this->redrawControl('modalComponentSnippet');
	}

	protected function createComponentTripStatisticModalControl(): TripStatisticModalRendererControl
	{
		if ($this->tripId === null) {
			throw new LogicException('Variable $tripId is null, please set that before creating component');
		}

		return $this->tripStatisticModalRendererControlFactory->create($this->tripId);
	}

	private function redrawTable(): void
	{
		if ($this->presenter->isAjax()) {
			$this->redrawControl('table');
			$this->redrawControl('footer');
		} else {
			$this->redirect('this');
		}
	}
}
