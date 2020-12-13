<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Request;

$container = require __DIR__ . '/../../TestsBootstrap.php';

use App\Request\RabbitMQ\MessageFactory;
use App\Request\Request;
use App\Request\RequestType;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\Transport\Prague\Parking\ParkingType;
use App\Transport\Prague\Request\RabbitMQ\ParkingLot\ParkingLotConsumer;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\GolemioService;
use Mistrfilda\Pid\Api\Parking\ParkingLot\ParkingLot;
use Mistrfilda\Pid\Api\Parking\ParkingLot\ParkingLotResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

class ParkingLotConsumerTest extends BaseTest
{
	private ParkingLotConsumer $parkingLotConsumer;

	private MessageFactory $messageFactory;

	private ParkingLotRepository $parkingLotRepository;

	public function testConsumeMessage(): void
	{
		$request = new Request(
			RequestType::PRAGUE_PARKING_LOT,
			$this->now
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$messageContents = $this->messageFactory->getMessage($request);

		$mockedMessage = new Message(
			'tag',
			'tag',
			false,
			'parkingLot',
			'routingKey',
			[],
			$messageContents
		);

		Assert::count(0, $this->parkingLotRepository->findAll());
		$rabbitCode = $this->parkingLotConsumer->consume($mockedMessage);

		$this->entityManager->refresh($request);
		Assert::notNull($request->getFinishedAt());
		Assert::null($request->getFailedAt());
		Assert::true($request->hasFinished());
		Assert::false($request->hasFailed());

		Assert::same(IConsumer::MESSAGE_ACK, $rabbitCode);

		$parkingLots = $this->parkingLotRepository->findAll();
		Assert::count(4, $parkingLots);
		$this->entityManager->refresh($parkingLots[0]);
		$this->entityManager->refresh($parkingLots[1]);
		Assert::count(1, $parkingLots[0]->getParkingLotOccupancies());
		Assert::count(1, $parkingLots[1]->getParkingLotOccupancies());

		Assert::same(100, $parkingLots[0]->getLastParkingLotOccupancy()->getTotalSpaces());
		Assert::same(30, $parkingLots[0]->getLastParkingLotOccupancy()->getFreeSpaces());
		Assert::same(50, $parkingLots[0]->getLastParkingLotOccupancy()->getOccupiedSpaces());

		//Failed
		$request = new Request(
			RequestType::PRAGUE_VEHICLE_POSITION,
			$this->now
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$messageContents = $this->messageFactory->getMessage($request);

		$mockedMessage = new Message(
			'tag',
			'tag',
			false,
			'parkingLot',
			'routingKey',
			[],
			$messageContents
		);

		$rabbitCode = $this->parkingLotConsumer->consume($mockedMessage);

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
		$this->parkingLotConsumer = $this->container->getByType(ParkingLotConsumer::class);
		$this->messageFactory = $this->container->getByType(MessageFactory::class);
		$this->parkingLotRepository = $this->container->getByType(ParkingLotRepository::class);
	}

	protected function mockTestSpecificClasses(): void
	{
		$firstResponse = Mockery::mock(ParkingLotResponse::class)
			->makePartial();

		$firstResponse->shouldReceive('getCount')->andReturn(4);
		$firstResponse->shouldReceive('getParkingLots')->andReturn([
			new ParkingLot(
				'1234',
				50.111,
				16.222,
				'TEstovaci parkoviste 1',
				'P + R parkoviste',
				(int) ParkingType::PARK_AND_RIDE,
				'Testovaci parkoviste 1',
				time(),
				100,
				50,
				30,
				null,
				[]
			),
			new ParkingLot(
				'2234',
				50.111,
				16.222,
				'TEstovaci parkoviste 2',
				'P + R parkoviste',
				(int) ParkingType::PARK_AND_RIDE,
				'Testovaci parkoviste 2',
				time(),
				100,
				50,
				30,
				null,
				[]
			),
			new ParkingLot(
				'3234',
				50.111,
				16.222,
				'TEstovaci parkoviste 3',
				'P + R parkoviste',
				(int) ParkingType::PARK_AND_RIDE,
				'Testovaci parkoviste 3',
				time(),
				100,
				50,
				30,
				null,
				[]
			),
			new ParkingLot(
				'4234',
				50.111,
				16.222,
				'TEstovaci parkoviste 4',
				'P + R parkoviste',
				(int) ParkingType::PARK_AND_RIDE,
				'Testovaci parkoviste 4',
				time(),
				100,
				50,
				30,
				null,
				[]
			),
		]);

		$mockedPidService = Mockery::mock(GolemioService::class);
		$mockedPidService
			->shouldReceive('sendGetParkingLotRequest')
			->once()->andReturn($firstResponse);

		$mockedPidService
			->shouldReceive('sendGetParkingLotRequest')
			->once()->andThrow(InvalidArgumentException::class, 'test exception');

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new ParkingLotConsumerTest($container))->run();
}
