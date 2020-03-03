<?php

declare(strict_types = 1);


namespace Test\Integration;


use Nette\DI\Container;
use Tester\TestCase;


require __DIR__ .'/Bootstrap.php';


/**
 * @skip
 */
abstract class BaseTest extends TestCase
{
	/** @var Container */
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}
}