<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Statistic;

$container = require __DIR__ . '/../../TestsBootstrap.php';

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\Transport\Prague\Statistic\TripStatisticFacade;
use App\Transport\Prague\Vehicle\Import\VehicleImportFacade;
use App\Transport\Prague\Vehicle\VehiclePosition;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Transport\Prague\Vehicle\VehicleRepository;
use App\Transport\Prague\Vehicle\VehicleType;
use Mistrfilda\Pid\Api\GolemioService;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePosition as PIDVehiclePosition;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePositionResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

class GenerateStatisticTest extends BaseTest
{
	private VehiclePositionRepository $vehiclePositionRepository;

	private VehicleImportFacade $vehicleImportFacade;

	private VehicleRepository $vehicleRepository;

	private TripStatisticFacade $tripStatisticFacade;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	public function testGenerateStatistic(): void
	{
		Assert::null($this->vehiclePositionRepository->findLast());
		Assert::noError(function (): void {
			$this->vehicleImportFacade->import();
		});

		/** @var VehiclePosition $vehiclePosition */
		$vehiclePosition = $this->vehiclePositionRepository->findLast();
		Assert::notNull($vehiclePosition);

		Assert::count(4, $vehiclePosition->getVehicles());
		Assert::same(4, $vehiclePosition->getVehiclesCount());

		Assert::noError(function (): void {
			$this->vehicleImportFacade->import();
		});

		Assert::count(7, $this->vehicleRepository->findAll());

		Assert::noError(function (): void {
			$this->tripStatisticFacade->processStatistics(2, 2, $this->now);
		});

		Assert::count(3, $this->tripStatisticDataRepository->findAll());
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->vehiclePositionRepository = $this->container->getByType(VehiclePositionRepository::class);
		$this->vehicleImportFacade = $this->container->getByType(VehicleImportFacade::class);
		$this->vehicleRepository = $this->container->getByType(VehicleRepository::class);
		$this->tripStatisticFacade = $this->container->getByType(TripStatisticFacade::class);
		$this->tripStatisticDataRepository = $this->container->getByType(TripStatisticDataRepository::class);
	}

	protected function mockTestSpecificClasses(): void
	{
		$firstResponse = Mockery::mock(VehiclePositionResponse::class)
			->makePartial();

		$firstResponse->shouldReceive('getCount')->andReturn(4);
		$firstResponse->shouldReceive('getVehiclePositions')->andReturn([
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L123',
				'1234',
				'123-01-01',
				VehicleType::CITY_BUS,
				1222,
				true,
				180,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L122',
				'1233',
				'122-01-01',
				VehicleType::INTERCITY_BUS,
				1222,
				false,
				231,
				'U12345',
				'U54321',
				'ZASTAVKA', 50,
				true
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L332',
				'1233',
				'332-01-01',
				VehicleType::INTERCITY_BUS,
				1222,
				true,
				-26,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L122',
				'1233',
				'122-01-03',
				VehicleType::CITY_BUS,
				1222,
				true,
				0,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
		]);

		$secondResponse = Mockery::mock(VehiclePositionResponse::class)
			->makePartial();

		$secondResponse->shouldReceive('getCount')->andReturn(4);
		$secondResponse->shouldReceive('getVehiclePositions')->andReturn([
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L123',
				'1234',
				'123-01-01',
				VehicleType::CITY_BUS,
				1222,
				true,
				122,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L122',
				'1233',
				'122-01-01',
				VehicleType::INTERCITY_BUS,
				1222,
				false,
				231,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L122',
				'1233',
				'122-01-03',
				VehicleType::CITY_BUS,
				1222,
				true,
				0,
				'U12345',
				'U54321',
				'ZASTAVKA',
				50,
				true
			),
		]);

		$mockedPidService = Mockery::mock(GolemioService::class);
		$mockedPidService->shouldReceive('sendGetVehiclePositionRequest')
			->andReturnValues([
				$firstResponse,
				$secondResponse,
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new GenerateStatisticTest($container))->run();
}
