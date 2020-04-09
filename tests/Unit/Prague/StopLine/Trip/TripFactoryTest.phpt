<?php

declare(strict_types=1);

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\Trip\TripFactory;
use Mistrfilda\Pid\Api\Trip\Trip as PIDTrip;
use Tester\Assert;

require __DIR__ . '/../../../Bootstrap.php';


$tripFactory = new TripFactory();

$testStop = new Stop(
	'123456',
	'U123456',
	50.01,
	15.01
);

$serviceId = '11112-0';
$tripId = '113-123-11';
$tripHeadsign = 'Testovací stanice';
$date = new DateTimeImmutable();
$lineNumber = '113';

$trip = $tripFactory->create(
	$testStop,
	$serviceId,
	$tripId,
	$tripHeadsign,
	true,
	new DateTimeImmutable(),
	$lineNumber
);

Assert::equal($serviceId, $trip->getServiceId());
Assert::equal($tripId, $trip->getTripId());
Assert::equal($tripHeadsign, $trip->getTripHeadsign());
Assert::equal($date->getTimestamp(), $trip->getDate()->getTimestamp());
Assert::true($trip->isWheelchairAccessible());
Assert::equal($lineNumber, $trip->getLineNumber());
Assert::equal($date->getTimestamp() . '_' . $tripId, $trip->getDateTripId());

$pidTrip = new PIDTrip($lineNumber, $serviceId, $tripId, $tripHeadsign, true);

$trip = $tripFactory->createFromPidLibrary($pidTrip, $testStop, $date);

Assert::equal($serviceId, $trip->getServiceId());
Assert::equal($tripId, $trip->getTripId());
Assert::equal($tripHeadsign, $trip->getTripHeadsign());
Assert::equal($date->getTimestamp(), $trip->getDate()->getTimestamp());
Assert::true($trip->isWheelchairAccessible());
Assert::equal($lineNumber, $trip->getLineNumber());
Assert::equal($date->getTimestamp() . '_' . $tripId, $trip->getDateTripId());
