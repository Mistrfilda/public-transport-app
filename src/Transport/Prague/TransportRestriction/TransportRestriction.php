<?php

declare(strict_types=1);

namespace App\Transport\Prague\TransportRestriction;

use App\Doctrine\CreatedAt;
use App\Doctrine\IEntity;
use App\Doctrine\SimpleUuid;
use App\Doctrine\UpdatedAt;
use App\Transport\TransportRestriction\ITransportRestriction;
use Doctrine\ORM\Mapping as ORM;
use JsonException;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Nette\Utils\Json;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_transport_restriction")
 */
class TransportRestriction implements IEntity, ITransportRestriction
{
	use SimpleUuid;
	use CreatedAt;
	use UpdatedAt;

	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	private string $transportRestrictionId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $type;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $active;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $title;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private ?string $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $link;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $publishDate;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $validFrom;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $validTo;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $priority;

	/**
	 * Json with lines
	 * @ORM\Column(type="text")
	 */
	private string $affectedLines;

	/**
	 * TransportRestriction constructor.
	 * @param string[] $affectedLines
	 */
	public function __construct(
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
		array $affectedLines,
		DateTimeImmutable $now
	) {
		$this->id = Uuid::uuid4();
		$this->createdAt = $now;
		$this->updatedAt = $now;

		$this->transportRestrictionId = $transportRestrictionId;
		$this->type = $type;
		$this->active = $active;
		$this->title = $title;
		$this->description = $description;
		$this->link = $link;
		$this->publishDate = $publishDate;
		$this->validFrom = $validFrom;
		$this->validTo = $validTo;
		$this->priority = $priority;
		$this->affectedLines = Json::encode($affectedLines);
	}

	/**
	 * @param string[] $affectedLines
	 * @throws JsonException
	 */
	public function update(
		bool $active,
		string $title,
		?string $description,
		?string $link,
		?DateTimeImmutable $publishDate,
		?DateTimeImmutable $validFrom,
		?DateTimeImmutable $validTo,
		int $priority,
		array $affectedLines,
		DateTimeImmutable $now
	): void {
		$this->updatedAt = $now;
		$this->link = $link;
		$this->active = $active;
		$this->title = $title;
		$this->description = $description;
		$this->publishDate = $publishDate;
		$this->validFrom = $validFrom;
		$this->validTo = $validTo;
		$this->priority = $priority;
		$this->affectedLines = Json::encode($affectedLines);
	}

	public function disable(DateTimeImmutable $now): void
	{
		$this->updatedAt = $now;
		$this->active = false;
	}

	public function getRestrictionId(): string
	{
		return $this->transportRestrictionId;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getLink(): ?string
	{
		return $this->link;
	}

	public function getPublishDate(): ?DateTimeImmutable
	{
		return $this->publishDate;
	}

	public function getSystemCreatedDate(): DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function getRestrictionValidFrom(): ?DateTimeImmutable
	{
		return $this->validFrom;
	}

	public function getRestrictionValidTo(): ?DateTimeImmutable
	{
		return $this->validTo;
	}

	/**
	 * @return string[]
	 * @throws JsonException
	 */
	public function getAffectedLines(): array
	{
		return Json::decode($this->affectedLines, Json::FORCE_ARRAY);
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function getLastCheckDate(): DateTimeImmutable
	{
		return $this->updatedAt;
	}

	public function getPriority(): int
	{
		return $this->priority;
	}
}
