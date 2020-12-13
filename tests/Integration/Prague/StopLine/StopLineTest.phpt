<?php

declare(strict_types=1);

namespace Test\Integration\Prague\StopLine;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopLine;
use App\Transport\Prague\StopLine\StopLineFactory;
use App\Transport\Prague\StopLine\StopTime\StopTime;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Transport\Prague\StopLine\Trip\Trip;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class StopLineTest extends BaseTest
{
	private StopLineFactory $stopLineFactory;

	private Stop $testStop;

	private StopTimeRepository $stopTimeRepository;

	private TripRepository $tripRepository;

	public function testStopLine(): void
	{
		$date = $this->today->addDaysToDatetime(2);

		$trips = [
			new Trip(
				$this->testStop,
				'1111-01',
				'333_1104_200301',
				'Testov',
				true,
				$date,
				'L333'
			),
			new Trip(
				$this->testStop,
				'1111-02',
				'333_1104_200302',
				'Testov',
				true,
				$date,
				'L333'
			),
			new Trip(
				$this->testStop,
				'1111-03',
				'333_1104_200303',
				'Testov',
				true,
				$date,
				'L333'
			),
			new Trip(
				$this->testStop,
				'1111-04',
				'333_1104_200304',
				'Testov',
				true,
				$date,
				'L333'
			),
		];

		foreach ($trips as $trip) {
			$this->entityManager->persist($trip);
		}

		$stopTimes = [
			new StopTime(
				$this->testStop,
				$date->setTime(12, 55, 0),
				$date->setTime(12, 55, 0),
				$date,
				'333_1104_200301',
				4
			),
			new StopTime(
				$this->testStop,
				$date->setTime(15, 55, 0),
				$date->setTime(15, 55, 0),
				$date,
				'333_1104_200302',
				4
			),
			new StopTime(
				$this->testStop,
				$date->setTime(19, 35, 0),
				$date->setTime(19, 35, 0),
				$date,
				'333_1104_200303',
				4
			),
			new StopTime(
				$this->testStop,
				$date->setTime(22, 55, 0),
				$date->setTime(22, 55, 0),
				$date,
				'333_1104_200304',
				4
			),
		];

		foreach ($stopTimes as $stopTime) {
			$this->entityManager->persist($stopTime);
		}

		$this->entityManager->flush();

		Assert::count(4, $this->tripRepository->findAll());
		Assert::count(4, $this->stopTimeRepository->findAll());

		$stopLines = $this->stopLineFactory->getStopLinesForStop($this->testStop);
		Assert::count(4, $stopLines);

		$this->assertStopLine($stopTimes[0], $trips[0], $stopLines[0]);
		$this->assertStopLine($stopTimes[1], $trips[1], $stopLines[1]);
		$this->assertStopLine($stopTimes[2], $trips[2], $stopLines[2]);
		$this->assertStopLine($stopTimes[3], $trips[3], $stopLines[3]);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->stopLineFactory = $this->container->getByType(StopLineFactory::class);
		$this->stopTimeRepository = $this->container->getByType(StopTimeRepository::class);
		$this->tripRepository = $this->container->getByType(TripRepository::class);

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

	private function assertStopLine(StopTime $expectedStopTime, Trip $expectedTrip, StopLine $stopLine): void
	{
		Assert::equal($expectedStopTime->getStop()->getStopId(), $stopLine->getStop()->getStopId());
		Assert::equal(
			$expectedStopTime->getDepartureTime()->getTimestamp(),
			$stopLine->getDepartureTime()->getTimestamp()
		);
		Assert::equal($expectedStopTime->getTripId(), $stopLine->getTripId());

		Assert::equal($expectedTrip->getTripId(), $stopLine->getTripId());
		Assert::equal($expectedTrip->getLineNumber(), $stopLine->getLineNumber());
		Assert::equal($expectedTrip->getTripHeadsign(), $stopLine->getFinalDestination());
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new StopLineTest($container))->run();
}
