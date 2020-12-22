<?php

declare(strict_types=1);

namespace Test\Integration\Prague\StopLine\StopTime;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopTime\Import\StopTimeImportFacade;
use App\Transport\Prague\StopLine\StopTime\StopTime;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\GolemioService;
use Mistrfilda\Pid\Api\StopTime\StopTime as PIDStopTime;
use Mistrfilda\Pid\Api\StopTime\StopTimeResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../../TestsBootstrap.php';

class StopTimeTest extends BaseTest
{
	private StopTimeRepository $stopTimeRepository;

	private StopTimeImportFacade $stopTimeImportFacade;

	private Stop $testStop;

	public function testImport(): void
	{
		Assert::noError(function (): void {
			$this->stopTimeImportFacade->import($this->testStop->getId(), 2);
		});

		$stopTimes = $this->stopTimeRepository->findAll();

		Assert::count(6, $stopTimes);

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

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->addDaysToDatetime(1)->setTime(12, 55, 0),
				$this->today->addDaysToDatetime(1)->setTime(12, 55, 0),
				$this->today->addDaysToDatetime(1),
				'333_1104_200302',
				4
			),
			$stopTimes[2]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->addDaysToDatetime(1)->setTime(13, 55, 0),
				$this->today->addDaysToDatetime(1)->setTime(13, 55, 0),
				$this->today->addDaysToDatetime(1),
				'333_1104_200303',
				4
			),
			$stopTimes[3]
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->addDaysToDatetime(1)->setTime(18, 55, 0),
				$this->today->addDaysToDatetime(1)->setTime(18, 55, 0),
				$this->today->addDaysToDatetime(1),
				'333_1104_200304',
				4
			),
			$stopTimes[4]
		);

		Assert::same(
			$this->today->addDaysToDatetime(1)->getTimestamp(),
			$stopTimes[4]->getDate()->getTimestamp()
		);

		$this->assertStopTime(
			new StopTime(
				$this->testStop,
				$this->today->addDaysToDatetime(1)->setTime(2, 55, 0),
				$this->today->addDaysToDatetime(1)->setTime(2, 55, 0),
				$this->today->addDaysToDatetime(1),
				'333_1104_200305',
				4
			),
			$stopTimes[5]
		);

		Assert::same(
			$this->today->addDaysToDatetime(1)->getTimestamp(),
			$stopTimes[5]->getDate()->getTimestamp()
		);

		$departureTableValues = $this->stopTimeRepository->findForDepartureTable(
			$this->testStop->getId(), $this->now->deductDaysFromDatetime(3)
		);

		Assert::count(6, $departureTableValues);
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

		$secondResponse->shouldReceive('getCount')->andReturn(4);
		$secondResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('12:55:00', '12:55:00', '333_1104_200302', 4),
			new PIDStopTime('13:55:00', '13:55:00', '333_1104_200303', 4),
			new PIDStopTime('18:55:00', '18:55:00', '333_1104_200304', 4),
			new PIDStopTime('26:55:00', '26:55:00', '333_1104_200305', 4),
		]);

		$mockedPidService = Mockery::mock(GolemioService::class);
		$mockedPidService->shouldReceive('sendGetStopTimesRequest')
			->andReturnValues([
				$firstResponse,
				$secondResponse,
				new InvalidArgumentException('Out of mocked responses'),
			]);

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}

	private function assertStopTime(StopTime $expected, StopTime $actual): void
	{
		Assert::same($expected->getStop()->getId(), $actual->getStop()->getId());
		Assert::same($expected->getArrivalTime()->getTimestamp(), $actual->getArrivalTime()->getTimestamp());
		Assert::same($expected->getDepartureTime()->getTimestamp(), $actual->getDepartureTime()->getTimestamp());
		Assert::same($expected->getTripId(), $actual->getTripId());
		Assert::same($expected->getStopSequence(), $actual->getStopSequence());
		Assert::same($expected->getDateTripId(), $actual->getDateTripId());
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new StopTimeTest($container))->run();
}
