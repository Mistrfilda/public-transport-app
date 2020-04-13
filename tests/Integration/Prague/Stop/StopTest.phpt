<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Stop;

use App\Transport\Prague\Stop\Filter\StopIdFilter;
use App\Transport\Prague\Stop\Import\StopImportFacade;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\Stop\StopCacheService;
use App\Transport\Prague\Stop\StopFactory;
use App\Transport\Prague\Stop\StopRepository;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\PidService;
use Mistrfilda\Pid\Api\Stop\Stop as PIDStop;
use Mistrfilda\Pid\Api\Stop\StopResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class StopTest extends BaseTest
{
	/** @var StopRepository */
	private $stopRepository;

	/** @var StopFactory */
	private $stopFactory;

	/** @var StopImportFacade */
	private $stopImportFacade;

	/** @var StopCacheService */
	private $stopCacheService;

	public function testImport(): void
	{
		Assert::noError(function (): void {
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

		Assert::noError(function (): void {
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

	public function testStopCacheService(): void
	{
		Assert::equal('Testovací zastávka 4 (U56789)', $this->stopCacheService->getStop('U56789'));
		Assert::equal('Testovací zastávka 2 (U54321)', $this->stopCacheService->getStop('U54321'));
		Assert::equal(StopCacheService::UNDEFINED_STOP_PLACEHOLDER, $this->stopCacheService->getStop('U123333'));
	}

	public function testStopIdFilter(): void
	{
		$filter = new StopIdFilter($this->stopCacheService);
		Assert::equal('Testovací zastávka 4 (U56789)', $filter->format('U56789'));
		Assert::equal('Testovací zastávka 2 (U54321)', $filter->format('U54321'));
		Assert::equal(StopCacheService::UNDEFINED_STOP_PLACEHOLDER, $filter->format(null));
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->stopRepository = $this->container->getByType(StopRepository::class);
		$this->stopFactory = $this->container->getByType(StopFactory::class);
		$this->stopImportFacade = $this->container->getByType(StopImportFacade::class);
		$this->stopCacheService = $this->container->getByType(StopCacheService::class);
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

		$emptyResponse = Mockery::mock(StopResponse::class)
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
				new InvalidArgumentException('Out of mocked responses'),
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}

	private function assertStops(Stop $expected, Stop $actual): void
	{
		Assert::equal($expected->getStopId(), $actual->getStopId());
		Assert::equal($expected->getName(), $actual->getName());
		Assert::equal($expected->getFormattedName(), $actual->getFormattedName());
		Assert::equal($expected->getCoordinates()->getLatitude(), $actual->getCoordinates()->getLatitude());
		Assert::equal($expected->getCoordinates()->getLongitude(), $expected->getCoordinates()->getLongitude());
	}
}

(new StopTest($container))->run();
