<?php

use App\Utils\Coordinates;
use Tester\Assert;


require __DIR__ . '/../Bootstrap.php';

$coordinates = new Coordinates(50.1252, 19.421);

Assert::equal(50.1252, $coordinates->getLatitude());
Assert::equal(19.421, $coordinates->getLongitude());