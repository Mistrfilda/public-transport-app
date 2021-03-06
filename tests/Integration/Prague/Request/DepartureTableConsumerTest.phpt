<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Request;

use App\Request\RabbitMQ\MessageFactory;
use App\Request\Request;
use App\Request\RequestRepository;
use App\Request\RequestType;
use App\Transport\Prague\DepartureTable\DepartureTable;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Request\RabbitMQ\DepartureTable\DepartureTableConsumer;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopLineFactory;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\GolemioService;
use Mistrfilda\Pid\Api\StopTime\StopTime as PIDStopTime;
use Mistrfilda\Pid\Api\StopTime\StopTimeResponse;
use Mistrfilda\Pid\Api\Trip\Trip as PIDTrip;
use Mistrfilda\Pid\Api\Trip\TripResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class DepartureTableConsumerTest extends BaseTest
{
	private StopLineFactory $stopLineFactory;

	private DepartureTableConsumer $departureTableConsumer;

	private MessageFactory $messageFactory;

	private RequestRepository $requestRepository;

	private DepartureTableRepository $departureTableRepository;

	public function testConsumeMessage(): void
	{
		$stop = new Stop(
			'U zahrady',
			'U123456',
			50.111,
			16.222
		);

		$this->entityManager->persist($stop);
		$this->entityManager->flush();
		$this->entityManager->refresh($stop);

		Assert::notNull($stop->getId());

		$departureTable = new DepartureTable(
			$stop,
			3,
			$this->now
		);

		$this->entityManager->persist($departureTable);
		$this->entityManager->flush();
		$this->entityManager->refresh($departureTable);

		$departureTableId = $departureTable->getId();

		Assert::notNull($stop->getId());

		$request = new Request(
			RequestType::PRAGUE_DEPARTURE_TABLE,
			$this->now,
			$departureTable
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$requestId = $request->getId();

		$mockedMessage = new Message(
			'tag',
			'tag',
			false,
			'departureTable',
			'routingKey',
			[],
			$this->messageFactory->getMessage($request)
		);

		Assert::count(0, $this->stopLineFactory->getStopLinesForStop($stop));
		$rabbitCode = $this->departureTableConsumer->consume($mockedMessage);

		$request = $this->requestRepository->findById($requestId);

		Assert::same(IConsumer::MESSAGE_ACK, $rabbitCode);
		Assert::count(6, $this->stopLineFactory->getStopLinesForStop($stop));

		$this->entityManager->refresh($request);
		Assert::notNull($request->getFinishedAt());
		Assert::null($request->getFailedAt());
		Assert::true($request->hasFinished());
		Assert::false($request->hasFailed());

		$departureTable = $this->departureTableRepository->findById($departureTableId);

		$request = new Request(
			RequestType::PRAGUE_DEPARTURE_TABLE,
			$this->now,
			$departureTable
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$requestId = $request->getId();

		$mockedMessage = new Message(
			'tag',
			'tag',
			false,
			'departureTable',
			'routingKey',
			[],
			$this->messageFactory->getMessage($request)
		);

		$rabbitCode = $this->departureTableConsumer->consume($mockedMessage);

		$request = $this->requestRepository->findById($requestId);
		Assert::same(IConsumer::MESSAGE_ACK, $rabbitCode);
		$this->entityManager->refresh($request);
		Assert::null($request->getFinishedAt());
		Assert::notNull($request->getFailedAt());
		Assert::false($request->hasFinished());
		Assert::true($request->hasFailed());
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->stopLineFactory = $this->container->getByType(StopLineFactory::class);
		$this->departureTableConsumer = $this->container->getByType(DepartureTableConsumer::class);
		$this->messageFactory = $this->container->getByType(MessageFactory::class);
		$this->requestRepository = $this->container->getByType(RequestRepository::class);
		$this->departureTableRepository = $this->container->getByType(DepartureTableRepository::class);
	}

	protected function mockTestSpecificClasses(): void
	{
		parent::mockTestSpecificClasses();
		$firstStoptimeResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$firstStoptimeResponse->shouldReceive('getCount')->andReturn(0);
		$firstStoptimeResponse->shouldReceive('getStopTimes')->andReturn([]);

		$secondStoptimeResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$secondStoptimeResponse->shouldReceive('getCount')->andReturn(2);
		$secondStoptimeResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('14:55:00', '12:55:00', '113-11-22', 4),
			new PIDStopTime('13:55:00', '13:55:00', '113-11-23', 4),
		]);

		$thirdStoptimeResponse = Mockery::mock(StopTimeResponse::class)
			->makePartial();

		$thirdStoptimeResponse->shouldReceive('getCount')->andReturn(4);
		$thirdStoptimeResponse->shouldReceive('getStopTimes')->andReturn([
			new PIDStopTime('12:55:00', '12:55:00', '113-11-22', 4),
			new PIDStopTime('13:55:00', '13:55:00', '113-11-23', 4),
			new PIDStopTime('18:55:00', '18:55:00', '113-11-24', 4),
			new PIDStopTime('26:55:00', '26:55:00', '113-11-25', 4),
		]);

		$firstTripResponse = Mockery::mock(TripResponse::class)
			->makePartial();

		$firstTripResponse->shouldReceive('getCount')->andReturn(0);
		$firstTripResponse->shouldReceive('getTrips')->andReturn([]);

		$secondTripResponse = Mockery::mock(TripResponse::class)
			->makePartial();

		$secondTripResponse->shouldReceive('getCount')->andReturn(2);
		$secondTripResponse->shouldReceive('getTrips')->andReturn([
			new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
			new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
		]);

		$thirdTripResponse = Mockery::mock(TripResponse::class)
			->makePartial();

		$thirdTripResponse->shouldReceive('getCount')->andReturn(4);
		$thirdTripResponse->shouldReceive('getTrips')->andReturn([
			new PIDTrip('113', '111101-1', '113-11-22', 'Test', true),
			new PIDTrip('113', '111101-2', '113-11-23', 'Test', false),
			new PIDTrip('113', '111101-3', '113-11-24', 'Test', true),
			new PIDTrip('113', '111101-4', '113-11-25', 'Test', false),

		]);

		$mockedPidService = Mockery::mock(GolemioService::class);
		$mockedPidService
			->shouldReceive('sendGetStopTimesRequest')
			->once()
			->andReturn($firstStoptimeResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTimesRequest')
			->once()
			->andReturn($secondStoptimeResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTimesRequest')
			->once()
			->andReturn($thirdStoptimeResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTimesRequest')
			->once()
			->andThrow(InvalidArgumentException::class, 'Exception!');

		$mockedPidService
			->shouldReceive('sendGetStopTripsRequest')
			->once()
			->andReturn($firstTripResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTripsRequest')
			->once()
			->andReturn($secondTripResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTripsRequest')
			->once()
			->andReturn($thirdTripResponse);

		$mockedPidService
			->shouldReceive('sendGetStopTripsRequest')
			->once()
			->andThrow(InvalidArgumentException::class, 'Exception!');

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new DepartureTableConsumerTest($container))->run();
}
