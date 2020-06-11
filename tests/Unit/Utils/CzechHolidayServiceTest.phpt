<?php

declare(strict_types=1);

use App\Utils\Datetime\CzechHolidayService;
use Tester\Assert;

require __DIR__ . '/../Bootstrap.php';

$czechHolidayService = new CzechHolidayService();

Assert::count(13, $czechHolidayService->getYearHolidays(2020));
Assert::count(13, $czechHolidayService->getYearHolidays(2021));

Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2020, 12, 24)));
Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2020, 5, 1)));
Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2020, 5, 8)));

Assert::true($czechHolidayService->isDateHoliday(17, 11, 2020));
Assert::true($czechHolidayService->isDateHoliday(28, 10, 2020));

Assert::notNull($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2020, 12, 26)));
Assert::notNull($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2020, 1, 1)));
Assert::null($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2020, 12, 28)));
Assert::null($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2020, 6, 5)));

Assert::notNull($czechHolidayService->getCzechHolidayByDayMonthYear(17, 11, 2020));
Assert::notNull($czechHolidayService->getCzechHolidayByDayMonthYear(28, 10, 2020));

Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2021, 12, 24)));
Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2021, 4, 2)));
Assert::true($czechHolidayService->isDateTimeHoliday((new DateTimeImmutable())->setDate(2021, 4, 5)));

Assert::true($czechHolidayService->isDateHoliday(17, 11, 2021));
Assert::true($czechHolidayService->isDateHoliday(28, 10, 2021));

Assert::notNull($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2021, 7, 5)));
Assert::notNull($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2021, 7, 6)));
Assert::null($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2021, 12, 28)));
Assert::null($czechHolidayService->getCzechHolidayByDatetime((new DateTimeImmutable())->setDate(2021, 6, 5)));

Assert::notNull($czechHolidayService->getCzechHolidayByDayMonthYear(17, 11, 2021));
Assert::notNull($czechHolidayService->getCzechHolidayByDayMonthYear(28, 10, 2021));
