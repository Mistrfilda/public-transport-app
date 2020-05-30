<?php

declare(strict_types=1);

namespace Test\Integration\Request;

use App\Request\RabbitMQ\MessageFactory;
use App\Request\Request;
use App\Request\RequestConditions;
use App\Request\RequestException;
use App\Request\RequestRepository;
use App\Request\RequestType;
use App\Transport\Prague\DepartureTable\DepartureTable;
use App\Transport\Prague\Request\RabbitMQ\DepartureTable\DepartureTableProducer;
use App\Transport\Prague\Request\RabbitMQ\VehiclePosition\VehiclePositionProducer;
use App\Transport\Prague\Request\RequestFacade;
use App\Transport\Prague\Stop\Stop;
use Mockery;
use Nette\Utils\Json;
use Test\Integration\BaseTest;
use Tester\Assert;
use Tester\Environment;

$container = require __DIR__ . '/../TestsBootstrap.php';

class RequestTest extends BaseTest
{
	private const PRODUCERS = [
		'pragueDepartureTableProducer' => DepartureTableProducer::class,
		'pragueVehiclePositionProducer' => VehiclePositionProducer::class,
	];

	private MessageFactory $messageFactory;

	private RequestFacade $requestFacade;

	private RequestRepository $requestRepository;

	private Stop $testStop;

	public function testCreateRequest(): void
	{
		$request = new Request(
			RequestType::PRAGUE_VEHICLE_POSITION,
			$this->now
		);

		Assert::equal($this->now, $request->getCreatedAt());
		Assert::equal(RequestType::PRAGUE_VEHICLE_POSITION, $request->getType());

		Assert::exception(function (): void {
			new Request('aaa', $this->now);
		}, RequestException::class, 'Invalid request type');
	}

	public function testVehiclePositionMessageFactory(): void
	{
		$request = new Request(
			RequestType::PRAGUE_VEHICLE_POSITION,
			$this->now
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$message = $this->messageFactory->getMessage($request);

		Assert::type('string', $message);

		$decodedMessage = Json::decode($message, Json::FORCE_ARRAY);
		Assert::count(2, $decodedMessage);
		Assert::equal($request->getId(), $decodedMessage['requestId']);
		Assert::equal($request->getCreatedAt()->getTimestamp(), $decodedMessage['dateTimestamp']);
	}

	public function testRequestCreate(): void
	{
		$this->requestFacade->generateRequests(new RequestConditions());
		$requests = $this->requestRepository->findAll();
		Assert::count(1, $requests);

		//Test if request is not created second time
		$this->requestFacade->generateRequests(new RequestConditions());
		$requests = $this->requestRepository->findAll();
		Assert::count(1, $requests);

		$testDepartureTable = new DepartureTable(
			$this->testStop,
			3,
			$this->now
		);

		$this->entityManager->persist($testDepartureTable);
		$this->entityManager->flush();

		$this->requestFacade->generateRequests(new RequestConditions());
		$requests = $this->requestRepository->findAll();
		Assert::count(2, $requests);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->messageFactory = $this->container->getByType(MessageFactory::class);
		$this->requestFacade = $this->container->getByType(RequestFacade::class);
		$this->requestRepository = $this->container->getByType(RequestRepository::class);

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
		parent::mockTestSpecificClasses();
		foreach (self::PRODUCERS as $key => $producer) {
			$mockedProducer = Mockery::mock($producer);
			$mockedProducer->shouldReceive('publish')->andReturn(0);
			$this->container->removeService($key);
			$this->container->addService($key, $mockedProducer);
		}
	}
}

if (getenv(Environment::RUNNER) === '1') {
	(new RequestTest($container))->run();
}
