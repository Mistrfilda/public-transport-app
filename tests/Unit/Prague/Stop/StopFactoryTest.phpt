<?php

declare(strict_types=1);

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\Stop\StopFactory;
use Ofce\Pid\Api\Stop\Stop as PIDStop;
use Tester\Assert;

require __DIR__ . '/../../Bootstrap.php';

$stopFactory = new StopFactory();

$name = 'test-stop-1';
$stopId = 'U123467';
$latitude = 55.1234;
$longitude = 15.1234;

$expectedStop = new Stop(
    $name,
    $stopId,
    $latitude,
    $longitude
);

Assert::equal($name, $expectedStop->getName());
Assert::equal($stopId, $expectedStop->getStopId());
Assert::equal($latitude, $expectedStop->getCoordinates()->getLatitude());
Assert::equal($longitude, $expectedStop->getCoordinates()->getLongitude());

$stop = $stopFactory->create(
    $name,
    $stopId,
    $latitude,
    $longitude
);

assertStops($expectedStop, $stop);

$name = 'test-stop-1';
$stopId = 'U123467';
$latitude = 55.1234;
$longitude = 15.1234;

$expectedStop = new Stop(
    $name,
    $stopId,
    $latitude,
    $longitude
);

$pidLibraryStop = new PIDStop(
    $stopId,
    $latitude,
    $longitude,
    $name
);

$stop = $stopFactory->createFromPidLibrary($pidLibraryStop);

assertStops($expectedStop, $stop);

function assertStops(Stop $expected, Stop $actual): void
{
    Assert::equal($expected->getStopId(), $actual->getStopId());
    Assert::equal($expected->getName(), $actual->getName());
    Assert::equal($expected->getFormattedName(), $actual->getFormattedName());
    Assert::equal($expected->getCoordinates()->getLatitude(), $actual->getCoordinates()->getLatitude());
    Assert::equal($expected->getCoordinates()->getLongitude(), $expected->getCoordinates()->getLongitude());
}
