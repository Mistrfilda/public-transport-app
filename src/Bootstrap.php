<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;

class Bootstrap
{
	public static function boot(bool $consoleMode = false): Configurator
	{
		$configurator = new Configurator();

//		set_error_handler(function ($severity, $message, $file, $line) {
//			throw new \ErrorException($message, $severity, $severity, $file, $line);
//		});

		if (array_key_exists('pubtransport', $_COOKIE) && $_COOKIE['pubtransport'] === 'debug_on') {
			$configurator->setDebugMode('192.168.1.13');
		}

		if ($consoleMode) {
			$configurator->setDebugMode(true);
		}

		$configurator->setDebugMode(true);
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/../config/config.neon');

		$rabbitEnvVariable = getenv('CI_RABBIT_MQ_QUEUE');
		if (
			($rabbitEnvVariable === false || $rabbitEnvVariable === 'FALSE')
			&& is_file(__DIR__ . '/../config/config.rabbitmq.neon')
		) {
			$configurator->addConfig(__DIR__ . '/../config/config.rabbitmq.neon');
		}

		if (is_file(__DIR__ . '/../config/config.local.neon')) {
			$configurator->addConfig(__DIR__ . '/../config/config.local.neon');
		}

		return $configurator;
	}
}
