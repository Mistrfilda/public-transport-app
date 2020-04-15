<?php

declare(strict_types=1);

namespace Test\Integration\Prague\Request;

use App\Request\RabbitMQ\MessageFactory;
use App\Request\Request;
use App\Request\RequestType;
use App\Transport\Prague\Request\RabbitMQ\VehiclePosition\VehiclePositionConsumer;
use App\Transport\Prague\Vehicle\VehiclePosition;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Transport\Prague\Vehicle\VehicleType;
use Bunny\Message;
use Gamee\RabbitMQ\Consumer\IConsumer;
use InvalidArgumentException;
use Mistrfilda\Pid\Api\PidService;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePosition as PIDVehiclePosition;
use Mistrfilda\Pid\Api\VehiclePosition\VehiclePositionResponse;
use Mockery;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../../TestsBootstrap.php';

class VehiclePositionConsumerTest extends BaseTest
{
	/** @var VehiclePositionConsumer */
	private $vehiclePositionConsumer;

	/** @var VehiclePositionRepository */
	private $vehiclePositionRepository;

	/** @var MessageFactory */
	private $messageFactory;

	public function testConsumeMessage(): void
	{
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
			'vehiclePosition',
			'routingKey',
			[],
			$messageContents
		);

		Assert::null($this->vehiclePositionRepository->findLast());
		$rabbitCode = $this->vehiclePositionConsumer->consume($mockedMessage);

		Assert::equal(IConsumer::MESSAGE_ACK, $rabbitCode);

		/** @var VehiclePosition $vehiclePosition */
		$vehiclePosition = $this->vehiclePositionRepository->findLast();

		Assert::notNull($vehiclePosition);
		Assert::count(4, $vehiclePosition->getVehicles());

		$this->entityManager->refresh($request);
		Assert::notNull($request->getFinishedAt());
		Assert::null($request->getFailedAt());
		Assert::true($request->hasFinished());
		Assert::false($request->hasFailed());

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
			'departureTable',
			'routingKey',
			[],
			$messageContents
		);

		$rabbitCode = $this->vehiclePositionConsumer->consume($mockedMessage);

		Assert::equal(IConsumer::MESSAGE_ACK, $rabbitCode);
		$this->entityManager->refresh($request);
		Assert::null($request->getFinishedAt());
		Assert::notNull($request->getFailedAt());
		Assert::false($request->hasFinished());
		Assert::true($request->hasFailed());
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->vehiclePositionConsumer = $this->container->getByType(VehiclePositionConsumer::class);
		$this->messageFactory = $this->container->getByType(MessageFactory::class);
		$this->vehiclePositionRepository = $this->container->getByType(VehiclePositionRepository::class);
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

		$mockedPidService = Mockery::mock(PidService::class);
		$mockedPidService
			->shouldReceive('sendGetVehiclePositionRequest')
			->once()->andReturn($firstResponse);

		$mockedPidService
			->shouldReceive('sendGetVehiclePositionRequest')
			->once()->andThrow(InvalidArgumentException::class, 'test exception');

		$this->container->removeService('pidservice');
		$this->container->addService('pidservice', $mockedPidService);
	}
}

//if (getenv(Environment::RUNNER) === '1') {
	(new VehiclePositionConsumerTest($container))->run();
//}
