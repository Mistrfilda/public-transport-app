<?php

declare(strict_types=1);

namespace Test\Integration;

use App\Utils\Datetime\DatetimeFactory;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Nette\DI\Container;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../../vendor/autoload.php';


abstract class BaseTest extends TestCase
{
	protected Container $container;

	protected Connection $connection;

	protected EntityManagerInterface $entityManager;

	protected DateTimeImmutable $now;

	protected DateTimeImmutable $today;

	public function __construct(Container $container)
	{
		Environment::lock('db', __DIR__ . '/../../temp');
		$this->container = $container;

		$this->connection = $container->getByType(Connection::class);
		$this->entityManager = $container->getByType(EntityManagerInterface::class);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->mockDatetimeFactory();
		$this->mockTestSpecificClasses();
		$this->connection->beginTransaction();
	}

	protected function tearDown(): void
	{
		parent::tearDown();
		$this->connection->rollBack();
	}

	protected function mockTestSpecificClasses(): void
	{
	}

	protected function mockDatetimeFactory(int $secondsToAdd = 0): void
	{
		$this->now = (new DateTimeImmutable())->modify('+ ' . $secondsToAdd . ' seconds');
		$dateTimeFactory = Mockery::mock(DatetimeFactory::class);
		$dateTimeFactory->shouldReceive('createNow')->andReturn($this->now);

		$this->today = $this->now->setTime(0, 0, 0);
		$dateTimeFactory->shouldReceive('createToday')->andReturn($this->today);
		$this->container->removeService('datetimefactory');
		$this->container->addService('datetimefactory', $dateTimeFactory);
	}
}
