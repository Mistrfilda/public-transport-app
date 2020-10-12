<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueTransportRestriction\Datagrid;

use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\Transport\Prague\TransportRestriction\TransportRestrictionRepository;
use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;
use Nette\Utils\Html;

class PragueTransportRestrictionDatagridFactory
{
	private AdminDatagridFactory $adminDatagridFactory;

	private TransportRestrictionRepository $transportRestrictionRepository;

	public function __construct(
		AdminDatagridFactory $adminDatagridFactory,
		TransportRestrictionRepository $transportRestrictionRepository
	) {
		$this->adminDatagridFactory = $adminDatagridFactory;
		$this->transportRestrictionRepository = $transportRestrictionRepository;
	}

	public function create(): AdminDatagrid
	{
		$grid = $this->adminDatagridFactory->create();
		$grid->setDataSource($this->transportRestrictionRepository->createQueryBuilder());

		$grid->addColumnText('restrictionId', 'Restriction ID');
		$grid->addColumnText('type', 'Type')
			->setRenderer(function (TransportRestriction $transportRestriction): string {
				return TransportRestrictionType::OPTIONS[$transportRestriction->getType()];
			})
			->setSortable()
			->setFilterSelect([null => 'Select'] + TransportRestrictionType::OPTIONS);
		$grid->addColumnText('title', 'Title')->setFilterText();

		$grid->addColumnDateTime('createdAt', 'Created at')->setSortable();
		$grid->addColumnDateTime('updatedAt', 'Updated at')->setSortable();

		$grid->addColumnText('active', 'Active')->setRenderer(function (TransportRestriction $transportRestriction) {
			if ($transportRestriction->isActive()) {
				return 'Yes';
			}

			return 'No';
		})->setSortable()->setFilterSelect(AdminDatagrid::BOOL_OPTIONS);

		$grid->addColumnText('lines', 'Affected lines')->setRenderer(
			function (TransportRestriction $transportRestriction): Html {
				$el = Html::el();
				foreach ($transportRestriction->getAffectedLines() as $line) {
					$el->addHtml(Html::el('span', [
						'class' => 'badge badge-secondary',
					])->setText($line));
				}

				return $el;
			}
		);

		$grid->addAction('detail', 'Show detail', 'showDetail!')
			->setIcon('eye')
			->setClass('btn btn-sm btn-info ajax');

		return $grid;
	}
}
