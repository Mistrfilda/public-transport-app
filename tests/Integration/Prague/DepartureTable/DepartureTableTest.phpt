<?php

declare(strict_types=1);

namespace Test\Integration\Prague\DepartureTable;

use App\Request\RequestRepository;
use App\Transport\Prague\DepartureTable\DepartureTableFacade;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Stop\Stop;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class DepartureTableTest extends BaseTest
{
	private DepartureTableRepository $departureTableRepository;

	private DepartureTableFacade $departureTableFacade;

	private Stop $testStop;

	private RequestRepository $requestRepository;

	public function testCreateUpdateDelete(): void
	{
		Assert::count(0, $this->departureTableRepository->findAll());
		Assert::count(0, $this->requestRepository->findAll());

		$firstDepartureTable = $this->departureTableFacade->createDepartureTable(
			$this->testStop->getId(),
			2
		);

		$secondsDepartureTable = $this->departureTableFacade->createDepartureTable(
			$this->testStop->getId(),
			5
		);

		$allDepartureTables = $this->departureTableRepository->findAll();
		Assert::count(2, $allDepartureTables);
		Assert::count(2, $this->requestRepository->findAll());

		Assert::same(2, $this->departureTableRepository->findById($firstDepartureTable->getId())->getNumberOfFutureDays());
		Assert::same(5, $this->departureTableRepository->findById($secondsDepartureTable->getId())->getNumberOfFutureDays());

		Assert::noError(function () use ($firstDepartureTable, $secondsDepartureTable): void {
			$this->departureTableFacade->updateDepartureTable(
				$firstDepartureTable->getId()->toString(),
				7
			);

			$this->departureTableFacade->updateDepartureTable(
				$secondsDepartureTable->getId()->toString(),
				14
			);
		});

		$allDepartureTables = $this->departureTableRepository->findAll();
		Assert::count(2, $allDepartureTables);
		Assert::count(2, $this->requestRepository->findAll());

		Assert::same(7, $this->departureTableRepository->findById($firstDepartureTable->getId())->getNumberOfFutureDays());
		Assert::same(14, $this->departureTableRepository->findById($secondsDepartureTable->getId())->getNumberOfFutureDays());

		Assert::noError(function () use ($secondsDepartureTable): void {
			$this->departureTableFacade->deleteDepartureTable(
				$secondsDepartureTable->getId()->toString()
			);
		});

		$allDepartureTables = $this->departureTableRepository->findAll();
		Assert::count(1, $allDepartureTables);
		Assert::count(2, $this->requestRepository->findAll());

		Assert::same(7, $this->departureTableRepository->findById($firstDepartureTable->getId())->getNumberOfFutureDays());
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->departureTableFacade = $this->container->getByType(DepartureTableFacade::class);
		$this->departureTableRepository = $this->container->getByType(DepartureTableRepository::class);
		$this->requestRepository = $this->container->getByType(RequestRepository::class);

		$testStop = new Stop(
			'Testovaci zastavka',
			'U123456789',
			50.01,
			15.01
		);

		$this->entityManager->persist($testStop);
		$this->entityManager->flush();
		$this->entityManager->refresh($testStop);
		$this->testStop = $testStop;
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new DepartureTableTest($container))->run();
}
