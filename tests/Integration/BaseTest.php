<?php

declare(strict_types=1);

namespace Test\Integration;

use App\Utils\DatetimeFactory;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Nette\DI\Container;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/TestsBootstrap.php';

/**
 * @skip
 */
abstract class BaseTest extends TestCase
{
	/** @var Container */
	protected $container;

	/** @var Connection */
	protected $connection;

	/** @var EntityManagerInterface */
	protected $entityManager;

	/** @var DateTimeImmutable */
	protected $now;

	/** @var DateTimeImmutable */
	protected $today;

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->mockDatetimeFactory();
		$this->mockTestSpecificClasses();

		$this->connection = $container->getByType(Connection::class);
		$this->connection->beginTransaction();

		$this->entityManager = $container->getByType(EntityManagerInterface::class);
		Environment::lock('db', __DIR__ . '/../../temp');
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
