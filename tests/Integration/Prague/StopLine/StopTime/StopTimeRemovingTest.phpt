<?php

declare(strict_types=1);

namespace Test\Integration\Prague\StopLine\StopTime;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopTime\Import\StopTimeImportFacade;
use App\Transport\Prague\StopLine\StopTime\StopTime;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\PidService;
use Mistrfilda\Pid\Api\StopTime\StopTime as PIDStopTime;
use Mistrfilda\Pid\Api\StopTime\StopTimeResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;

$container = require __DIR__ . '/../../../TestsBootstrap.php';

class StopTimeRemovingTest extends BaseTest
{
	/** @var StopTimeRepository */
	private $stopTimeRepository;

	/** @var StopTimeImportFacade */
	private $stopTimeImportFacade;

	/** @var Stop */
	private $testStop;

	public function testImport(): void
	{
		Assert::noError(function (): void {
			$this->stopTimeImportFacade->import($this->testStop->getId(), 1);
		});

		$stopTimes = $this->stopTimeRepository->findAllSorted();
		Assert::count(2, $stopTimes);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(12, 55, 0),
				$this->today->setTime(12, 55, 0),
				$this->today,
				'333_1104_200302',
				4
			),
			$stopTimes[0]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(13, 55, 0),
				$this->today->setTime(13, 55, 0),
				$this->today,
				'333_1104_200303',
				4
			),
			$stopTimes[1]
		);

		Assert::noError(function (): void {
			$this->stopTimeImportFacade->import($this->testStop->getId(), 1);
		});

		$stopTimes = $this->stopTimeRepository->findAllSorted();
		Assert::count(7, $stopTimes);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(1, 35, 0),
				$this->today->setTime(1, 35, 0),
				$this->today,
				'333_1104_200307',
				4
			),
			$stopTimes[0]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(2, 55, 0),
				$this->today->setTime(2, 55, 0),
				$this->today,
				'333_1104_200308',
				4
			),
			$stopTimes[1]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(12, 55, 0),
				$this->today->setTime(12, 55, 0),
				$this->today,
				'333_1104_200302',
				4
			),
			$stopTimes[2]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(20, 55, 0),
				$this->today->setTime(20, 55, 0),
				$this->today,
				'333_1104_200305',
				4
			),
			$stopTimes[5]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(21, 55, 0),
				$this->today->setTime(21, 55, 0),
				$this->today,
				'333_1104_200306',
				4
			),
			$stopTimes[6]
		);

		Assert::noError(function (): void {
			$this->stopTimeImportFacade->import($this->testStop->getId(), 1);
		});

		$stopTimes = $this->stopTimeRepository->findAllSorted();
		Assert::count(5, $stopTimes);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(2, 55, 0),
				$this->today->setTime(2, 55, 0),
				$this->today,
				'333_1104_200308',
				4
			),
			$stopTimes[0]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(12, 55, 0),
				$this->today->setTime(12, 55, 0),
				$this->today,
				'333_1104_200302',
				4
			),
			$stopTimes[1]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(13, 55, 0),
				$this->today->setTime(13, 55, 0),
				$this->today,
				'333_1104_200303',
				4
			),
			$stopTimes[2]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(20, 55, 0),
				$this->today->setTime(20, 55, 0),
				$this->today,
				'333_1104_200305',
				4
			),
			$stopTimes[3]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->setTime(21, 55, 0),
				$this->today->setTime(21, 55, 0),
				$this->today,
				'333_1104_200306',
				4
			),
			$stopTimes[4]
		);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->stopTimeRepository = $this->container->getByType(StopTimeRepository::class);
		$this->stopTimeImportFacade = $this->container->getByType(StopTimeImportFacade::class);

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
		$firstResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$firstResponse->shouldReceive('getCount')->andReturn(2);
		$firstResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('12:55:00', '12:55:00', '333_1104_200302', 4),
			new PIDStopTime('13:55:00', '13:55:00', '333_1104_200303', 4),
		]);

		$secondResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$secondResponse->shouldReceive('getCount')->andReturn(7);
		$secondResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('12:55:00', '12:55:00', '333_1104_200302', 4),
			new PIDStopTime('13:55:00', '13:55:00', '333_1104_200303', 4),
			new PIDStopTime('18:55:00', '18:55:00', '333_1104_200304', 4),
			new PIDStopTime('20:55:00', '20:55:00', '333_1104_200305', 4),
			new PIDStopTime('21:55:00', '21:55:00', '333_1104_200306', 4),
			new PIDStopTime('25:35:00', '25:35:00', '333_1104_200307', 4),
			new PIDStopTime('26:55:00', '26:55:00', '333_1104_200308', 4),
		]);

		$thirdResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$thirdResponse->shouldReceive('getCount')->andReturn(5);
		$thirdResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('12:55:00', '12:55:00', '333_1104_200302', 4),
			new PIDStopTime('13:55:00', '13:55:00', '333_1104_200303', 4),
			new PIDStopTime('20:55:00', '20:55:00', '333_1104_200305', 4),
			new PIDStopTime('21:55:00', '21:55:00', '333_1104_200306', 4),
			new PIDStopTime('26:55:00', '26:55:00', '333_1104_200308', 4),
		]);

		$mockedPidService = Mockery::mock(PidService::class);
		$mockedPidService->shouldReceive('sendGetStopTimesRequest')
			->andReturnValues([
				$firstResponse,
				$secondResponse,
				$thirdResponse,
				new InvalidArgumentException('Out of mocked responses'),
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}

	private function assertStopTime(StopTime $expected, StopTime $actual): void
	{
		Assert::equal($expected->getStop()->getId(), $actual->getStop()->getId());
		Assert::equal($expected->getArrivalTime()->getTimestamp(), $actual->getArrivalTime()->getTimestamp());
		Assert::equal($expected->getDepartureTime()->getTimestamp(), $actual->getDepartureTime()->getTimestamp());
		Assert::equal($expected->getTripId(), $actual->getTripId());
		Assert::equal($expected->getStopSequence(), $actual->getStopSequence());
		Assert::equal($expected->getDateTripId(), $actual->getDateTripId());
	}
}

(new StopTimeRemovingTest($container))->run();
