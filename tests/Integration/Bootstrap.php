<?php

use Nette\Configurator;
use Tester\Environment;


require __DIR__ . '/../../vendor/autoload.php';

Environment::setup();
Environment::setupColors();

$configurator = new Configurator();
$configurator->setDebugMode(TRUE);
//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();


$configurator->addConfig(__DIR__ . '/../../config/config.neon');
$configurator->addConfig(__DIR__ . '/config/tests.neon');

if (is_file(__DIR__ . '/config/tests.local.neon')) {
	$configurator->addConfig(__DIR__ . '/config/tests.local.neon');
}

return $configurator;