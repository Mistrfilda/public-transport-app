<?php

declare(strict_types=1);

namespace Test\Integration\Prague\StopLine\Trip;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\Trip\Import\TripImportFacade;
use App\Transport\Prague\StopLine\Trip\Trip;
use App\Transport\Prague\StopLine\Trip\TripFactory;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\GolemioService;
use Mistrfilda\Pid\Api\Trip\Trip as PIDTrip;
use Mistrfilda\Pid\Api\Trip\TripResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../../TestsBootstrap.php';

class TripTest extends BaseTest
{
	private TripRepository $tripRepository;

	private TripFactory $tripFactory;

	private TripImportFacade $tripImportFacade;

	private Stop $testStop;

	public function testImport(): void
	{
		Assert::noError(function (): void {
			$this->tripImportFacade->import($this->testStop->getId(), 1);
		});

		$trips = $this->tripRepository->findAll();

		Assert::count(2, $trips);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
				$this->testStop,
				$this->today
			),
			$trips[0]
		);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
				$this->testStop,
				$this->today
			),
			$trips[1]
		);

		Assert::noError(function (): void {
			$this->tripImportFacade->import($this->testStop->getId(), 1);
		});

		$trips = $this->tripRepository->findAll();
		Assert::count(4, $trips);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
				$this->testStop,
				$this->today
			),
			$trips[0]
		);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
				$this->testStop,
				$this->today
			),
			$trips[1]
		);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-3', '113-11-24', 'Test', true),
				$this->testStop,
				$this->today
			),
			$trips[2]
		);

		$this->assertTrips(
			$this->tripFactory->createFromPidLibrary(
				new PIDTrip('113', '111101-4', '113-11-25', 'Test', false),
				$this->testStop,
				$this->today
			),
			$trips[3]
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->tripRepository = $this->container->getByType(TripRepository::class);
		$this->tripFactory = $this->container->getByType(TripFactory::class);
		$this->tripImportFacade = $this->container->getByType(TripImportFacade::class);

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

	protected function mockTestSpecificClasses(): void
	{
		$firstResponse = Mockery::mock(TripResponse::class)
			->makePartial();

		$firstResponse->shouldReceive('getCount')->andReturn(2);
		$firstResponse->shouldReceive('getTrips')->andReturn([
			new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
			new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
		]);

		$secondResponse = Mockery::mock(TripResponse::class)
			->makePartial();

		$secondResponse->shouldReceive('getCount')->andReturn(4);
		$secondResponse->shouldReceive('getTrips')->andReturn([
			new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
			new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
			new PIDTrip('113', '111101-3', '113-11-24', 'Test', true),
			new PIDTrip('113', '111101-4', '113-11-25', 'Test', false),

		]);

		$mockedPidService = Mockery::mock(GolemioService::class);
		$mockedPidService->shouldReceive('sendGetStopTripsRequest')
			->andReturnValues([
				$firstResponse,
				$secondResponse,
				new InvalidArgumentException('Out of mocked responses'),
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}

	private function assertTrips(Trip $expected, Trip $actual): void
	{
		Assert::equal($expected->getTripId(), $actual->getTripId());
		Assert::equal($expected->getDateTripId(), $actual->getDateTripId());
		Assert::equal($expected->getDate()->getTimestamp(), $actual->getDate()->getTimestamp());
		Assert::equal($expected->getLineNumber(), $actual->getLineNumber());
		Assert::equal($expected->getTripHeadsign(), $actual->getTripHeadsign());
		Assert::equal($expected->getServiceId(), $actual->getServiceId());
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new TripTest($container))->run();
}
