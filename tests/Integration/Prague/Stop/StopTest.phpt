<?php

declare(strict_types = 1);


namespace Test\Integration\Prague\Stop;


use App\Transport\Prague\Stop\Import\StopImportFacade;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\Stop\StopFactory;
use App\Transport\Prague\Stop\StopRepository;
use InvalidArgumentException;
use Mockery;
use Ofce\Pid\Api\PidService;
use Ofce\Pid\Api\Stop\StopResponse;
use Test\Integration\BaseTest;
use Tester\Assert;
use Ofce\Pid\Api\Stop\Stop as PIDStop;


$container = require __DIR__ . '/../../TestsBootstrap.php';

class StopTest extends BaseTest
{
	/** @var StopRepository */
	private $stopRepository;

	/** @var StopFactory */
	private $stopFactory;

	/** @var StopImportFacade */
	private $stopImportFacade;

	public function testStopFactory(): void
	{
		$name = 'test-stop-1';
		$stopId = 'U123467';
		$latitude = 55.1234;
		$longitude = 15.1234;

		$expectedStop = new Stop(
			$name,
			$stopId,
			$latitude,
			$longitude
		);

		Assert::equal($name, $expectedStop->getName());
		Assert::equal($stopId, $expectedStop->getStopId());
		Assert::equal($latitude, $expectedStop->getCoordinates()->getLatitude());
		Assert::equal($longitude, $expectedStop->getCoordinates()->getLongitude());

		$stop = $this->stopFactory->create(
			$name,
			$stopId,
			$latitude,
			$longitude
		);

		$this->assertStops($expectedStop, $stop);
	}

	public function testStopFactoryFromPidLibrary(): void
	{
		$name = 'test-stop-1';
		$stopId = 'U123467';
		$latitude = 55.1234;
		$longitude = 15.1234;

		$expectedStop = new Stop(
			$name,
			$stopId,
			$latitude,
			$longitude
		);

		$pidLibraryStop = new PIDStop(
			$stopId,
			$latitude,
			$longitude,
			$name
		);

		$stop = $this->stopFactory->createFromPidLibrary($pidLibraryStop);

		$this->assertStops($expectedStop, $stop);
	}

	public function testImport(): void
	{
		Assert::noError(function () {
			$this->stopImportFacade->import();
		});

		$stops = $this->stopRepository->findAll();
		Assert::count(2, $stops);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U12345', 50.54321, 15.12345, 'Testovací zastávka 1'),
			),
			$stops[0]
		);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U54321', 50.12345, 15.54321, 'Testovací zastávka 2'),
				),
			$stops[1]
		);

		Assert::noError(function () {
			$this->stopImportFacade->import();
		});

		$stops = $this->stopRepository->findAll();
		Assert::count(4, $stops);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U12345', 50.54321, 15.12345, 'Testovací zastávka 1'),
				),
			$stops[0]
		);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U54321', 50.12345, 15.54321, 'Testovací zastávka 2'),
				),
			$stops[1]
		);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U98765', 50.98765, 15.56789, 'Testovací zastávka 3'),
				),
			$stops[2]
		);

		$this->assertStops(
			$this->stopFactory->createFromPidLibrary(
				new PIDStop('U56789', 50.56789, 15.98765, 'Testovací zastávka 4'),
				),
			$stops[3]
		);

	}

	private function assertStops(Stop $expected, Stop $actual): void
	{
		Assert::equal($expected->getStopId(), $actual->getStopId());
		Assert::equal($expected->getName(), $actual->getName());
		Assert::equal($expected->getFormattedName(), $actual->getFormattedName());
		Assert::equal($expected->getCoordinates()->getLatitude(), $actual->getCoordinates()->getLatitude());
		Assert::equal($expected->getCoordinates()->getLongitude(), $expected->getCoordinates()->getLongitude());
	}

	protected function setUp(): void
	{
		$this->stopRepository = $this->container->getByType(StopRepository::class);
		$this->stopFactory = $this->container->getByType(StopFactory::class);
		$this->stopImportFacade = $this->container->getByType(StopImportFacade::class);
	}

	protected function mockTestSpecificClasses(): void
	{
		$firstResponse = Mockery::mock(StopResponse::class)
			->makePartial();

		$firstResponse->shouldReceive('getCount')->andReturn(2);
		$firstResponse->shouldReceive('getStops')->andReturn([
			new PIDStop('U12345', 50.54321, 15.12345, 'Testovací zastávka 1'),
			new PIDStop('U54321', 50.12345, 15.54321, 'Testovací zastávka 2'),
		]);

		$secondResponse = Mockery::mock(StopResponse::class)
			->makePartial();

		$secondResponse->shouldReceive('getCount')->andReturn(4);
		$secondResponse->shouldReceive('getStops')->andReturn([
			new PIDStop('U12345', 50.54321, 15.12345, 'Testovací zastávka 1'),
			new PIDStop('U54321', 50.12345, 15.54321, 'Testovací zastávka 2'),
			new PIDStop('U98765', 50.98765, 15.56789, 'Testovací zastávka 3'),
			new PIDStop('U56789', 50.56789, 15.98765, 'Testovací zastávka 4'),

		]);

		$emptyResponse =  Mockery::mock(StopResponse::class)
			->makePartial();

		$emptyResponse->shouldReceive('getCount')->andReturn(0);
		$emptyResponse->shouldReceive('getStops')->andReturn([]);

		$mockedPidService = Mockery::mock(PidService::class);
		$mockedPidService->shouldReceive('sendGetStopsRequest')
			->andReturnValues([
				$firstResponse,
				$emptyResponse,
				$secondResponse,
				$emptyResponse,
				new InvalidArgumentException('Out of mocked responses')
			]);

		$this->container->removeService('pidservie');
		$this->container->addService('pidservie', $mockedPidService);
	}
}

(new StopTest($container))->run();