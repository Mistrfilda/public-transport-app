<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Utils\DatetimeFactory;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Psr\Log\LoggerInterface;
use Throwable;

class TripStatisticFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var DatetimeFactory */
    private $datetimeFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        DatetimeFactory $datetimeFactory
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->datetimeFactory = $datetimeFactory;
    }

    public function processStatistics(int $numberOfDays = 1): void
    {
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

        $yesterday = $this->datetimeFactory->createToday()->modify('- 1 day');
        $count = 0;
        while ($count < $numberOfDays) {
            $date = $yesterday;
            if ($count >= 1) {
                $date = $yesterday->modify('- ' . $count . ' days');
            }

            $this->logger->info(
                'Processing statistics for date',
                [
                    'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
                ]
            );

            $sql = "select 
pv.trip_id as 'trip_id', 
pv.route_id as 'route_id',  
pv.final_station as 'final_station',
pv.vehicle_type as 'vehicle_type',
pv.registration_number as 'registration_number', 
pv.company as 'company',
pv.wheelchair_accessible as 'wheelchair_accessible',
max(pv.delay_in_seconds) as 'highest_delay', 
avg(pv.delay_in_seconds) as 'average_delay',
max(pp.created_at) as 'oldest_known_position', 
min(pp.created_at) as 'newest_known_position',
count(pv.id) as 'position_count'
from prague_vehicle pv inner join prague_vehicle_position pp on pv.vehicle_position_id = pp.id 
where date(pp.created_at) = :created_date 
group by pv.trip_id, pv.route_id, pv.final_station, pv.vehicle_type, pv.registration_number, pv.company, pv.wheelchair_accessible 
having count(pv.id) > 5;
";

            $query = $this->entityManager->createNativeQuery($sql, $resultSetMapping);
            $query->setParameter('created_date', $date->format('Y-m-d'), Types::STRING);

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
                            $result['company'],
                            $result['registrationNumber'],
                            $result['vehicleType'],
                            $result['positionCount'],
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
                    ->executeUpdate(
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

                $this->entityManager->commit();
            } catch (Throwable $e) {
                $this->entityManager->rollback();
                $this->logger->emergency(
                    'Exception occured while inserting new statistic data',
                    [
                        'e' => $e->getMessage(),
                        'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
                    ]
                );

                throw $e;
            }
        }
        die();
    }
}