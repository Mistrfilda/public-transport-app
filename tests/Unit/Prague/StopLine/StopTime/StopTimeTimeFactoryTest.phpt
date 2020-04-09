<?php

declare(strict_types=1);

use App\Transport\Prague\StopLine\StopTime\InvalidTimeException;
use App\Transport\Prague\StopLine\StopTime\StopTimeTimeFactory;
use Tester\Assert;

require __DIR__ . '/../../../Bootstrap.php';


$stopTimeTimeFactory = new StopTimeTimeFactory();

$date = new DateTimeImmutable();

Assert::equal(
	$date->setTime(14, 55, 0)->getTimestamp(),
	$stopTimeTimeFactory->createDatetime($date, '14:55:00')->getTimestamp()
);
Assert::equal(
	$date->setTime(12, 30, 0)->getTimestamp(),
	$stopTimeTimeFactory->createDatetime($date, '12:30:00')->getTimestamp()
);

Assert::exception(function () use ($stopTimeTimeFactory, $date): void {
	$stopTimeTimeFactory->createDatetime($date, '14:55:32:31');
}, InvalidTimeException::class);

Assert::equal(
	$date->setTime(1, 0, 0)->getTimestamp(),
	$stopTimeTimeFactory->createDatetime($date, '25:00:00')->getTimestamp()
);
