<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\CreatedAt;
use App\Doctrine\IEntity;
use App\Doctrine\Uuid;
use App\Transport\Cities;
use App\Transport\Vehicle\IVehiclePosition;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_vehicle_position")
 */
class VehiclePosition implements IEntity, IVehiclePosition
{
	use Uuid;
	use CreatedAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $city;

	/**
	 * @var Collection<int,Vehicle>
	 * @ORM\OneToMany(targetEntity="App\Transport\Prague\Vehicle\Vehicle", mappedBy="vehiclePosition", indexBy="dateTripId")
	 */
	private Collection $vehicles;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $vehiclesCount = 0;

	public function __construct(
		DateTimeImmutable $now
	) {
		$this->createdAt = $now;
		$this->city = Cities::PRAGUE;
		$this->vehicles = new ArrayCollection();
	}

	public function updateVehiclesCount(int $count): void
	{
		$this->vehiclesCount = $count;
	}

	public function getCity(): string
	{
		return $this->city;
	}

	/**
	 * @return Vehicle[]
	 */
	public function getVehicles(): array
	{
		return $this->vehicles->toArray();
	}

	public function getVehiclesCount(): int
	{
		return $this->vehiclesCount;
	}
}
