<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Transport\Prague\Statistic\TripList\TripListFacade;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Holiday\CzechHolidayService;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Psr\Log\LoggerInterface;
use Throwable;

class TripStatisticFacade
{
	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private DatetimeFactory $datetimeFactory;

	private TripListFacade $tripListFacade;

	private CzechHolidayService $czechHolidayService;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		DatetimeFactory $datetimeFactory,
		TripListFacade $tripListFacade,
		CzechHolidayService $czechHolidayService
	) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->datetimeFactory = $datetimeFactory;
		$this->tripListFacade = $tripListFacade;
		$this->czechHolidayService = $czechHolidayService;
	}

	public function processStatistics(
		int $numberOfDays = 1,
		int $minimalNumberOfPositions = 5,
		?DatetimeImmutable $dateFrom = null
	): void {
		$resultSetMapping = new ResultSetMapping();
		$resultSetMapping->addScalarResult('trip_id', 'tripId');
		$resultSetMapping->addScalarResult('route_id', 'routeId');
		$resultSetMapping->addScalarResult('final_station', 'finalStation');
		$resultSetMapping->addScalarResult('vehicle_type', 'vehicleType', Types::INTEGER);
		$resultSetMapping->addScalarResult('registration_number', 'registrationNumber');
		$resultSetMapping->addScalarResult('company', 'company');
		$resultSetMapping->addScalarResult(
			'wheelchair_accessible',
			'wheelchairAccessible',
			Types::BOOLEAN
		);
		$resultSetMapping->addScalarResult('highest_delay', 'highestDelay', Types::INTEGER);
		$resultSetMapping->addScalarResult('average_delay', 'averageDelay', Types::FLOAT);
		$resultSetMapping->addScalarResult('last_position_delay', 'lastPositionDelay', Types::INTEGER);

		$resultSetMapping->addScalarResult(
			'oldest_known_position',
			'oldestKnownPosition',
			Types::DATETIME_IMMUTABLE
		);
		$resultSetMapping->addScalarResult(
			'newest_known_position',
			'newestKnownPosition',
			Types::DATETIME_IMMUTABLE
		);
		$resultSetMapping->addScalarResult('position_count', 'positionCount', Types::INTEGER);

		$sql = "select 
pv.trip_id as 'trip_id', 
pv.route_id as 'route_id',  
pv.final_station as 'final_station',
max(pv.vehicle_type) as 'vehicle_type',
pv.registration_number as 'registration_number', 
pv.company as 'company',
pv.wheelchair_accessible as 'wheelchair_accessible',
max(pv.delay_in_seconds) as 'highest_delay', 
avg(pv.delay_in_seconds) as 'average_delay',
max(pp.created_at) as 'oldest_known_position', 
min(pp.created_at) as 'newest_known_position',
count(pv.id) as 'position_count',
max(lp.delay_in_seconds) as 'last_position_delay'
from prague_vehicle pv 
inner join prague_vehicle_position pp on pv.vehicle_position_id = pp.id
left join (select trip_id, delay_in_seconds from prague_vehicle where id in (select max(id) from prague_vehicle pv group by pv.trip_id)) lp 
	on lp.trip_id = pv.trip_id 
where date(pp.created_at) = :created_date 
group by pv.trip_id, pv.route_id, pv.final_station, pv.registration_number, pv.company, pv.wheelchair_accessible 
having count(pv.id) >= :minimal_number_of_positions;
";

		if ($dateFrom === null) {
			$dateFrom = $this->datetimeFactory->createToday()->deductDaysFromDatetime(1);
		}

		$count = 0;
		while ($count < $numberOfDays) {
			$date = $dateFrom;
			if ($count >= 1) {
				$date = $dateFrom->deductDaysFromDatetime($count);
			}

			$isCzechHoliday = $this->czechHolidayService->isDateTimeHoliday($date);

			$this->logger->info(
				'Processing statistics for date',
				[
					'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
				]
			);

			$query = $this->entityManager->createNativeQuery($sql, $resultSetMapping);
			$query->setParameter('created_date', $date->format('Y-m-d'), Types::STRING);
			$query->setParameter('minimal_number_of_positions', $minimalNumberOfPositions);

			$data = $query->getResult();
			$this->logger->info('Successfully fetched results', [
				'data_count' => count($data),
			]);
			$count++;

			$this->entityManager->beginTransaction();
			try {
				$flushIndex = 0;
				foreach ($data as $result) {
					$this->entityManager->persist(
						new TripStatisticData(
							$result['tripId'],
							$result['routeId'],
							$result['finalStation'],
							$result['wheelchairAccessible'],
							$date,
							$result['oldestKnownPosition'],
							$result['newestKnownPosition'],
							$result['highestDelay'],
							(int) $result['averageDelay'],
							$result['lastPositionDelay'],
							$result['company'],
							$result['registrationNumber'],
							$result['vehicleType'],
							$result['positionCount'],
							$isCzechHoliday
						)
					);

					$flushIndex++;
					if ($flushIndex > 100) {
						$this->entityManager->flush();
						$flushIndex = 0;
					}
				}

				$this->logger->info('Successfully saved trip statistics');

				$this->entityManager->getConnection()
					->executeStatement(
						'
DELETE pv from prague_vehicle pv 
inner join prague_vehicle_position pp on pv.vehicle_position_id = pp.id
where date(pp.created_at) = :created_date 
				',
						[
							'created_date' => $date->format('Y-m-d'),
						],
						[
							'created_date' => Types::STRING,
						]
					);

				$this->logger->info('Successfully deleted trip statistics');

				$this->entityManager->flush();
				$this->entityManager->commit();
				$this->entityManager->clear();
			} catch (Throwable $e) {
				$this->entityManager->rollback();
				$this->logger->emergency(
					'Exception occured while inserting new statistic data',
					[
						'e' => $e->getMessage(),
						'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
					]
				);
				$this->entityManager->clear();
			}
		}

		$this->tripListFacade->generateTripList();
	}
}
