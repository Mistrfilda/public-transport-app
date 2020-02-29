<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopLineFactory;
use App\UI\Admin\Base\BaseControl;
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

    public function __construct(
        string $id,
        DepartureTableRepository $departureTableRepository,
        StopLineFactory $stopLineFactory
    ) {
        $this->id = Uuid::fromString($id);
        $this->departureTableRepository = $departureTableRepository;
        $this->stopLineFactory = $stopLineFactory;
    }

    public function render(): void
    {
        $departureTable = $this->departureTableRepository->findById($this->id);
        $this->getTemplate()->stopLines = $this->stopLineFactory->getStopLinesForStop($departureTable->getPragueStop());
        $this->getTemplate()->departureTable = $departureTable;
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    public function handleRefresh(): void
    {
        if ($this->presenter->isAjax()) {
            $this->redrawControl('table');
        } else {
            $this->redirect('this');
        }
    }
}
