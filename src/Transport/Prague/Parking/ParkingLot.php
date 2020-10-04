<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Doctrine\IEntity;
use App\Doctrine\SimpleUuid;
use App\Transport\ParkingLot\IParkingLot;
use App\Transport\ParkingLot\IParkingLotOccupancy;
use App\Utils\Coordinates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_parking_lot",
 *     indexes={
 *        @ORM\Index(name="parking_id", columns={"parking_id"}),
 *        @ORM\Index(name="address", columns={"address"})
 *	   },
 * )
 */
class ParkingLot implements IEntity, IParkingLot
{
	use SimpleUuid;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $parkingId;

	/**
	 * @ORM\Column(type="float")
	 */
	private float $latitude;

	/**
	 * @ORM\Column(type="float")
	 */
	private float $longitude;

	/**
	 * @var Collection<int, ParkingLotOccupancy>
	 * @ORM\OneToMany(targetEntity="App\Transport\Prague\Parking\ParkingLotOccupancy", mappedBy="parkingLot")
	 */
	private Collection $parkingLotOccupancies;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $address;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $type;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $paymentUrl;

	public function __construct(
		string $parkingId,
		float $latitude,
		float $longitude,
		string $address,
		string $type,
		string $name,
		?string $paymentUrl
	) {
		$this->id = Uuid::uuid4();

		$this->parkingId = $parkingId;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->address = $address;
		$this->type = $type;
		$this->name = $name;
		$this->paymentUrl = $paymentUrl;

		$this->parkingLotOccupancies = new ArrayCollection();
	}

	public function update(
		float $latitude,
		float $longitude,
		string $address,
		string $type,
		string $name,
		?string $paymentUrl
	): void {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->address = $address;
		$this->type = $type;
		$this->name = $name;
		$this->paymentUrl = $paymentUrl;
	}

	public function getCoordinates(): Coordinates
	{
		return new Coordinates($this->latitude, $this->longitude);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getParkingId(): string
	{
		return $this->parkingId;
	}

	public function getAddress(): string
	{
		return $this->address;
	}

	public function getParkingType(): string
	{
		return $this->type;
	}

	public function isParkAndRide(): bool
	{
		return $this->type === ParkingType::PARK_AND_RIDE;
	}

	/**
	 * @return array<int, ParkingLotOccupancy>
	 */
	public function getParkingLotOccupancies(): array
	{
		return $this->parkingLotOccupancies->toArray();
	}

	public function getLastParkingLotOccupancy(): IParkingLotOccupancy
	{
		$occupancy = $this->parkingLotOccupancies->last();
		if ($occupancy === false) {
			throw new ParkingLotException('No parking lot occupancy is available');
		}

		return $occupancy;
	}

	public function getPaymentUrl(): ?string
	{
		return $this->paymentUrl;
	}
}
