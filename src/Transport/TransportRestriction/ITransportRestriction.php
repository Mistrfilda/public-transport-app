<?php

declare(strict_types=1);

namespace App\Transport\TransportRestriction;

use Mistrfilda\Datetime\Types\DatetimeImmutable;

interface ITransportRestriction
{
	public function getRestrictionId(): string;

	public function getType(): string;

	public function getTitle(): string;

	public function getDescription(): ?string;

	public function getLink(): ?string;

	public function getPublishDate(): ?DateTimeImmutable;

	public function getSystemCreatedDate(): DateTimeImmutable;

	public function getRestrictionValidFrom(): ?DateTimeImmutable;

	public function getRestrictionValidTo(): ?DateTimeImmutable;

	public function getLastCheckDate(): DateTimeImmutable;

	/** @return string[] */
	public function getAffectedLines(): array;

	public function isActive(): bool;

	public function getPriority(): int;
}
