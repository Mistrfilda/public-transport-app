<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueTransportRestriction;

use App\Transport\Prague\TransportRestriction\TransportRestrictionRepository;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueTransportRestriction\Datagrid\PragueTransportRestrictionDatagridFactory;
use Ramsey\Uuid\Uuid;

class PragueTransportRestrictionPresenter extends AdminPresenter
{
	private PragueTransportRestrictionDatagridFactory $pragueTransportRestrictionDatagridFactory;

	private TransportRestrictionRepository $transportRestrictionRepository;

	public function __construct(
		PragueTransportRestrictionDatagridFactory $pragueTransportRestrictionDatagridFactory,
		TransportRestrictionRepository $transportRestrictionRepository
	) {
		parent::__construct();
		$this->pragueTransportRestrictionDatagridFactory = $pragueTransportRestrictionDatagridFactory;
		$this->transportRestrictionRepository = $transportRestrictionRepository;
	}

	public function handleShowDetail(string $id): void
	{
		$transportRestriction = $this->transportRestrictionRepository->getById(Uuid::fromString($id));

		$this->showModal(
			AdminPresenter::DEFAULT_MODAL_COMPONENT_NAME,
			null,
			null,
			['transportRestriction' => $transportRestriction],
			__DIR__ . '/templates/detailModal.latte'
		);
	}

	protected function createComponentTransportRestrictionGrid(): AdminDatagrid
	{
		return $this->pragueTransportRestrictionDatagridFactory->create();
	}
}
