<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Admin\Base\BaseControl;
use Ramsey\Uuid\Uuid;

class DepartureTableControl extends BaseControl
{
    /** @var DepartureTableRepository */
    private $departureTableRepository;

    public function __construct(
        string $id,
        DepartureTableRepository $departureTableRepository
    ) {
        $this->departureTableRepository = $departureTableRepository;
        bdump(Uuid::fromString($id));
        dump($this->departureTableRepository->findById(Uuid::fromString($id)));
    }

    public function render(): void
    {
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }
}
