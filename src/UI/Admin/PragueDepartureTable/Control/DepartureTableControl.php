<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopLineFactory;
use App\UI\Admin\Base\BaseControl;
use Nette\Http\Session;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DepartureTableControl extends BaseControl
{
    /** @var UuidInterface */
    private $id;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var StopLineFactory */
    private $stopLineFactory;

    /** @var DepartureTablePaginatorService */
    private $paginatorService;

    public function __construct(
        string $id,
        DepartureTableRepository $departureTableRepository,
        StopLineFactory $stopLineFactory,
        Session $session
    ) {
        $this->id = Uuid::fromString($id);
        $this->departureTableRepository = $departureTableRepository;
        $this->stopLineFactory = $stopLineFactory;
        $this->paginatorService = new DepartureTablePaginatorService($this->id, $session);
    }

    public function render(): void
    {
        $departureTable = $this->departureTableRepository->findById($this->id);
        $stoplines = $this->stopLineFactory->getStopLinesForStop(
            $departureTable->getPragueStop(),
            $this->paginatorService->getLoadedCount()
        );

        $showLoadMoreButton = true;
        if ($this->paginatorService->getLoadedCount() > count($stoplines)) {
            $showLoadMoreButton = false;
        }

        $this->getTemplate()->stopLines = $stoplines;
        $this->getTemplate()->departureTable = $departureTable;
        $this->getTemplate()->currentStep = $this->paginatorService->getCurrentStep();
        $this->getTemplate()->showLoadMoreButton = $showLoadMoreButton;
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
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

    private function redrawTable(): void
    {
        if ($this->presenter->isAjax()) {
            $this->redrawControl('table');
            $this->redrawControl('headerButtons');
            $this->redrawControl('footerButtons');
        } else {
            $this->redirect('this');
        }
    }
}
