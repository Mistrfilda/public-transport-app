<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();

        $configurator->enableTracy(__DIR__ . '/../log');

        //enable from local network IP
        if (array_key_exists('pubtransport', $_COOKIE) && $_COOKIE['pubtransport'] === 'debug_on') {
            $configurator->setDebugMode('192.168.1.13');
        }

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig(__DIR__ . '/../config/config.neon');

        $rabbitEnvVariable = getenv('TRAVIS_RABBIT_MQ_QUEUE');
        if (($rabbitEnvVariable === false || $rabbitEnvVariable === 'FALSE') && is_file(__DIR__ . '/../config/config.rabbitmq.neon')) {
            $configurator->addConfig(__DIR__ . '/../config/config.rabbitmq.neon');
        }

        if (is_file(__DIR__ . '/../config/config.local.neon')) {
            $configurator->addConfig(__DIR__ . '/../config/config.local.neon');
        }

        return $configurator;
    }
}
