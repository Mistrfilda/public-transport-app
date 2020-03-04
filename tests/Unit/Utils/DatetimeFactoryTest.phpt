<?php

use App\Utils\DatetimeFactory;
use Tester\Assert;


require __DIR__ . '/../Bootstrap.php';

$datetimeFactory = new DatetimeFactory();

$now = new DateTimeImmutable();

$factoryDatetimeNow = $datetimeFactory->createNow();

Assert::equal($now->format('Y'), $factoryDatetimeNow->format('Y'));
Assert::equal($now->format('m'), $factoryDatetimeNow->format('m'));
Assert::equal($now->format('d'), $factoryDatetimeNow->format('d'));

Assert::equal($now->format('H'), $factoryDatetimeNow->format('H'));
Assert::equal($now->format('i'), $factoryDatetimeNow->format('i'));

$factoryDatetimeToday = $datetimeFactory->createToday();

Assert::equal($now->format('Y'), $factoryDatetimeToday->format('Y'));
Assert::equal($now->format('m'), $factoryDatetimeToday->format('m'));
Assert::equal($now->format('d'), $factoryDatetimeToday->format('d'));

Assert::equal(0, (int) $factoryDatetimeToday->format('H'));
Assert::equal(0, (int) $factoryDatetimeToday->format('i'));
Assert::equal(0, (int) $factoryDatetimeToday->format('s'));