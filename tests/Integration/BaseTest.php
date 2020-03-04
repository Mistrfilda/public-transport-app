<?php

declare(strict_types=1);

namespace Test\Integration;

use App\Utils\DatetimeFactory;
use DateTimeImmutable;
use Mockery;
use Nette\DI\Container;
use Tester\TestCase;

require __DIR__ . '/TestsBootstrap.php';

/**
 * @skip
 */
abstract class BaseTest extends TestCase
{
    /** @var Container */
    protected $container;

    /** @var DateTimeImmutable */
    protected $now;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->mockBaseClasses();
        $this->mockTestSpecificClasses();
    }

    protected function mockTestSpecificClasses(): void
    {
    }

    private function mockBaseClasses(): void
    {
        $this->now = new DateTimeImmutable();
        $dateTimeFactory = Mockery::mock(DatetimeFactory::class);
        $dateTimeFactory->shouldReceive('createNow')->andReturn($this->now);
        $dateTimeFactory->shouldReceive('createToday')->andReturn($this->now->setTime(0, 0, 0));
        $this->container->removeService('datetimefactory');
        $this->container->addService('datetimefactory', $dateTimeFactory);
    }
}
