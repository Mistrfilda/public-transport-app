<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;

class Bootstrap
{
	public static function boot(bool $consoleMode = false): Configurator
	{
		$configurator = new Configurator();

		if (array_key_exists('pubtransport', $_COOKIE) && $_COOKIE['pubtransport'] === 'debug_on') {
			$configurator->setDebugMode('192.168.1.13');
		}

		if ($consoleMode) {
			$configurator->setDebugMode(true);
		}

		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/../config/config.neon');

		$rabbitEnvVariable = getenv('TRAVIS_RABBIT_MQ_QUEUE');
		if (
			($rabbitEnvVariable === false || $rabbitEnvVariable === 'FALSE')
			&& is_file(__DIR__ . '/../config/config.rabbitmq.neon')
		) {
			$configurator->addConfig(__DIR__ . '/../config/config.rabbitmq.neon');
		}

		$databasePort = getenv('DB_PORT');
		if ($databasePort !== false) {
			echo $databasePort;
			echo (int) $databasePort;
		}

		if (is_file(__DIR__ . '/../config/config.local.neon')) {
			$configurator->addConfig(__DIR__ . '/../config/config.local.neon');
		}

		return $configurator;
	}
}
