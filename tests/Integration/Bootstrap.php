<?php

declare(strict_types=1);

namespace Test\Integration;

use Nette\Configurator;
use Nette\DI\Container;

require __DIR__ . '/../../vendor/autoload.php';

class Bootstrap
{
	public static function boot(): Container
	{
		$configurator = new Configurator();
		$configurator->setDebugMode(true);
		//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
		$configurator->enableTracy(__DIR__ . '/../../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/../../config/config.neon');
		$configurator->addConfig(__DIR__ . '/config/tests.neon');

		if (is_file(__DIR__ . '/config/tests.local.neon')) {
			$configurator->addConfig(__DIR__ . '/config/tests.local.neon');
		}

		$ciEnv = getenv('CI_TESTS_ENV');
		if ($ciEnv !== false) {
			$configurator->addConfig(__DIR__ . '/../travis/test.local.neon');
		}

		return $configurator->createContainer();
	}
}
