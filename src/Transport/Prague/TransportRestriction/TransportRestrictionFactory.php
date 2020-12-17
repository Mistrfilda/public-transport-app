<?php

declare(strict_types=1);

namespace App\Transport\Prague\TransportRestriction;

use App\Transport\TransportRestriction\TransportRestrictionPriority;
use App\Transport\TransportRestriction\TransportRestrictionType;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Mistrfilda\Pid\Api\Rss\TransportRestriction\LongTerm\LongTermTransportRestriction;
use Mistrfilda\Pid\Api\Rss\TransportRestriction\ShortTerm\ShortTermTransportRestriction;

class TransportRestrictionFactory
{
	private DatetimeFactory $datetimeFactory;

	public function __construct(DatetimeFactory $datetimeFactory)
	{
		$this->datetimeFactory = $datetimeFactory;
	}

	/**
	 * @param string[] $affectedLines
	 */
	public function create(
		string $transportRestrictionId,
		string $type,
		bool $active,
		string $title,
		?string $description,
		?string $link,
		?DateTimeImmutable $publishDate,
		?DateTimeImmutable $validFrom,
		?DateTimeImmutable $validTo,
		int $priority,
		array $affectedLines
	): TransportRestriction {
		return new TransportRestriction(
			$transportRestrictionId,
			$type,
			$active,
			$title,
			$description,
			$link,
			$publishDate,
			$validFrom,
			$validTo,
			$priority,
			$affectedLines,
			$this->datetimeFactory->createNow()
		);
	}

	public function createFromShortTermPidLibrary(
		ShortTermTransportRestriction $shortTermTransportRestriction
	): TransportRestriction {
		$publishDate = null;
		if ($shortTermTransportRestriction->getPublishedDate() !== null) {
			$publishDate = $this->datetimeFactory->createFromTimestamp(
				$shortTermTransportRestriction->getPublishedDate()->getTimestamp()
			);
		}

		return $this->create(
			$shortTermTransportRestriction->getGuid(),
			TransportRestrictionType::SHORT_TERM,
			true,
			$shortTermTransportRestriction->getTitle(),
			$shortTermTransportRestriction->getDescription(),
			$shortTermTransportRestriction->getLink(),
			$publishDate,
			null,
			null,
			TransportRestrictionPriority::LEVEL_1,
			$shortTermTransportRestriction->getLines()
		);
	}

	public function createFromLongTermPidLibrary(
		LongTermTransportRestriction $longTermTransportRestriction
	): TransportRestriction {
		$dateTo = null;
		if ($longTermTransportRestriction->getDateToTimestamp() !== null) {
			$dateTo = $this->datetimeFactory->createFromTimestamp($longTermTransportRestriction->getDateToTimestamp());
		}

		return $this->create(
			$longTermTransportRestriction->getGuid(),
			TransportRestrictionType::LONG_TERM,
			true,
			$longTermTransportRestriction->getTitle(),
			$longTermTransportRestriction->getDescription(),
			$longTermTransportRestriction->getLink(),
			$this->datetimeFactory->createFromTimestamp(
				$longTermTransportRestriction->getPublishedDate()->getTimestamp()
			),
			$this->datetimeFactory->createFromTimestamp($longTermTransportRestriction->getDateFromTimestamp()),
			$dateTo,
			$longTermTransportRestriction->getPriority(),
			$longTermTransportRestriction->getLines()
		);
	}
}
