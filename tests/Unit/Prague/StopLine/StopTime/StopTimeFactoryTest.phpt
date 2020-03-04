<?php

declare(strict_types=1);

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopTime\StopTimeFactory;
use App\Transport\Prague\StopLine\StopTime\StopTimeTimeFactory;
use Ofce\Pid\Api\StopTime\StopTime as PIDStopTime;
use Tester\Assert;

require __DIR__ . '/../../../Bootstrap.php';

$stopTimeTimeFactory = new StopTimeTimeFactory();
$stopTimeFactory = new StopTimeFactory($stopTimeTimeFactory);


$testStop = new Stop(
    '123456',
    'U123456',
    50.01,
    15.01
);


$arrivalTime = '11:55:00';
$departureTime = '11:56:00';
$date = new DateTimeImmutable();
$tripId = '113-11-22';
$stopSequence = 8;

$stopTime = $stopTimeFactory->create(
    $testStop,
    $arrivalTime,
    $departureTime,
    $date,
    $tripId,
    $stopSequence
);

Assert::equal($stopTimeTimeFactory->createDatetime($date, $arrivalTime)->getTimestamp(), $stopTime->getArrivalTime()->getTimestamp());
Assert::equal($stopTimeTimeFactory->createDatetime($date, $departureTime)->getTimestamp(), $stopTime->getDepartureTime()->getTimestamp());
Assert::equal($tripId, $stopTime->getTripId());
Assert::equal($stopSequence, $stopTime->getStopSequence());

$pidStopTime = new PIDStopTime(
    $arrivalTime,
    $departureTime,
    $tripId,
    $stopSequence
);

$stopTime = $stopTimeFactory->createFromPidLibrary($pidStopTime, $testStop, $date);

Assert::equal($stopTimeTimeFactory->createDatetime($date, $arrivalTime)->getTimestamp(), $stopTime->getArrivalTime()->getTimestamp());
Assert::equal($stopTimeTimeFactory->createDatetime($date, $departureTime)->getTimestamp(), $stopTime->getDepartureTime()->getTimestamp());
Assert::equal($tripId, $stopTime->getTripId());
Assert::equal($stopSequence, $stopTime->getStopSequence());
