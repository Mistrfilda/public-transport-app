<?php

declare(strict_types=1);

namespace App\UI\Admin\Request\Datagrid;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Request\RequestType;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;
use App\UI\Admin\Base\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use Nette\Utils\Html;

class RequestDatagridFactory
{
	/** @var AdminDatagridFactory */
	private $datagridFactory;

	/** @var RequestRepository */
	private $requestRepository;

	/** @var DepartureTableRepository */
	private $pragueDepartureTableRepository;

	public function __construct(
		AdminDatagridFactory $datagridFactory,
		RequestRepository $requestRepository,
		DepartureTableRepository $departureTableRepository
	) {
		$this->datagridFactory = $datagridFactory;
		$this->requestRepository = $requestRepository;
		$this->pragueDepartureTableRepository = $departureTableRepository;
	}

	public function create(): AdminDatagrid
	{
		$grid = $this->datagridFactory->create();
		$qb = $this->requestRepository->createQueryBuilder();
		$qb->leftJoin('request.pragueDepartureTable', 'departureTable');
		$qb->leftJoin('departureTable.stop', 'stop');
		$grid->setDataSource($qb);

		$grid->addColumnText('id', 'ID')->setSortable()->setFilterText();

		$types = RequestType::getLabels();
		$type = $grid->addColumnText('type', 'Type')->setRenderer(function (Request $request) use ($types): string {
			if (! array_key_exists($request->getType(), $types)) {
				throw new InvalidArgumentException();
			}

			return $types[$request->getType()];
		});

		$grid->setFilterSelect($type, $types);

		$grid->addColumnDateTime('createdAt', 'Created at')
			->setRenderer(function (Request $request): string {
				return AdminDatagrid::formatNullableDatetimeColumn($request->getCreatedAt());
			})
			->setSortable()
			->setFilterDate();

		$grid->addColumnDateTime('finishedAt', 'Finished at')
			->setRenderer(function (Request $request): string {
				return AdminDatagrid::formatNullableDatetimeColumn($request->getFinishedAt());
			})
			->setFilterDate();

		$grid->addColumnDateTime('failedAt', 'Failed at')
			->setRenderer(function (Request $request): string {
				return AdminDatagrid::formatNullableDatetimeColumn($request->getFailedAt());
			})
			->setFilterDate();

		$departureTable = $grid->addColumnText('departureTableName', 'Departure table')
			->setRenderer(function (Request $request): string {
				if ($request->hasPragueDepartureTable()) {
					return $request->getPragueDepartureTable()->getAdminFormatedName();
				}

				return AdminDatagrid::NULLABLE_PLACEHOLDER;
			});

		$grid->setFilterSelect($departureTable, $this->pragueDepartureTableRepository->findPairs())
			->setCondition(function (QueryBuilder $qb, string $value): void {
				$qb->andWhere($qb->expr()->eq('departureTable.id', ':departureTableId'));
				$qb->setParameter('departureTableId', $value);
			});

		$grid->setRowCallback(function (Request $request, Html $row): void {
			if ($request->hasFinished()) {
				$row->addClass('table-success');
			} elseif ($request->hasFailed()) {
				$row->addClass('table-danger');
			} else {
				$row->addClass('table-info');
			}
		});

		$grid->setDefaultSort(['createdAt' => 'desc']);

		return $grid;
	}
}
