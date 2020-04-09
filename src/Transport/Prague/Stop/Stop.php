<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Stop\IStop;
use App\Utils\Coordinates;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prague_stop")
 */
class Stop implements IStop, IEntity
{
	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $stopId;

	/**
	 * @var float
	 * @ORM\Column(type="float")
	 */
	private $latitude;

	/**
	 * @var float
	 * @ORM\Column(type="float")
	 */
	private $longitude;

	public function __construct(
		string $name,
		string $stopId,
		float $latitude,
		float $longitude
	) {
		$this->name = $name;
		$this->stopId = $stopId;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	public function updateStop(
		string $name,
		float $latitude,
		float $longitude
	): void {
		$this->name = $name;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getStopId(): string
	{
		return $this->stopId;
	}

	public function getCoordinates(): Coordinates
	{
		return new Coordinates($this->latitude, $this->longitude);
	}

	public function getFormattedName(): string
	{
		return sprintf('%s (%s)', $this->getName(), $this->getStopId());
	}
}
