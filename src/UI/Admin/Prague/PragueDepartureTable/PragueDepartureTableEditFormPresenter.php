<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTable;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\Prague\PragueDepartureTable\Form\DepartureTableFormFactory;
use App\Utils\FlashMessageType;
use Ramsey\Uuid\Uuid;

class PragueDepartureTableEditFormPresenter extends AdminPresenter
{
	private DepartureTableFormFactory $departureTableFormFactory;

	public function __construct(DepartureTableFormFactory $departureTableFormFactory)
	{
		parent::__construct();
		$this->departureTableFormFactory = $departureTableFormFactory;
	}

	public function renderEdit(?string $id): void
	{
	}

	protected function createComponentDepartureTableForm(): AdminForm
	{
		$id = $this->getParameter('id');

		if ($id !== null) {
			$id = Uuid::fromString($id);
		}

		$onSuccess = function (DepartureTable $departureTable): void {
			$this->flashMessage(
				sprintf('Departure table %s successfuly saved', $departureTable->getId()->toString()),
				FlashMessageType::INFO
			);
			$this->redirect('default');
		};

		return $this->departureTableFormFactory->create($onSuccess, $id);
	}
}
