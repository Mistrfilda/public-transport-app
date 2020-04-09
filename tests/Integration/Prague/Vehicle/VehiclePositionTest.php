<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Vehicle;

use App\Transport\Prague\Vehicle\Import\VehicleImportFacade;
use App\Transport\Prague\Vehicle\VehiclePosition;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Transport\Prague\Vehicle\VehicleRepository;
use App\Transport\Prague\Vehicle\VehicleType;
use Mistrfilda\Pid\Api\PidService;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePosition as PIDVehiclePosition;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePositionResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class VehiclePositionTest extends BaseTest
{
	/**
	 * @var VehiclePositionRepository
	 */
	private $vehiclePositionRepository;

	/**
	 * @var VehicleImportFacade
	 */
	private $vehicleImportFacade;

	/** @var VehicleRepository */
	private $vehicleRepository;

	protected function setUp(): void
	{
		parent::setUp();
		$this->vehiclePositionRepository = $this->container->getByType(VehiclePositionRepository::class);
		$this->vehicleImportFacade = $this->container->getByType(VehicleImportFacade::class);
		$this->vehicleRepository = $this->container->getByType(VehicleRepository::class);
	}

	public function testVehiclePosition(): void
	{
		Assert::null($this->vehiclePositionRepository->findLast());
		Assert::noError(function (): void {
			$this->vehicleImportFacade->import();
		});

		/** @var VehiclePosition $vehiclePosition */
		$vehiclePosition = $this->vehiclePositionRepository->findLast();
		Assert::notNull($vehiclePosition);

		Assert::count(4, $vehiclePosition->getVehicles());

		Assert::noError(function (): void {
			$this->vehicleImportFacade->import();
		});

		Assert::count(6, $this->vehicleRepository->findAll());
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
				122,
				'U12345',
				'U54321',
				'ZASTAVKA'
			),
			new PIDVehiclePosition(
				50.001,
				15.002,
				'ARRIVA VLAKY',
				'ARRIVA',
				'L122',
				'1233',
				'122-01-02',
				VehicleType::INTERCITY_BUS,
				1222,
				false,
				231,
				'U12345',
				'U54321',
				'ZASTAVKA'
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
				'ZASTAVKA'
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
				'ZASTAVKA'
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
				'ZASTAVKA'
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
				'ZASTAVKA'
			),
		]);

		$mockedPidService = Mockery::mock(PidService::class);
		$mockedPidService->shouldReceive('sendGetVehiclePositionRequest')
			->andReturnValues([
				$firstResponse,
				$secondResponse,
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}
}

(new VehiclePositionTest($container))->run();
