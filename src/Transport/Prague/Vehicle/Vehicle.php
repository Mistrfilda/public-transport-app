<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Vehicle\IVehicle;
use App\Transport\Vehicle\IVehiclePosition;
use App\Utils\Coordinates;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_vehicle")
 */
class Vehicle implements IEntity, IVehicle
{
    use Identifier;

    /**
     * @var VehiclePosition
     * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Vehicle\VehiclePosition", inversedBy="vehicles")
     */
    private $vehiclePosition;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $routeId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $tripId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $dateTripId;

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

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $finalStation;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $delayInSeconds;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $wheelchairAccessible;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastStopId;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $nextStopId;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $registrationNumber;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $vehicleType;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $company;

    public function __construct(
        VehiclePosition $vehiclePosition,
        string $routeId,
        string $tripId,
        float $latitude,
        float $longitude,
        string $finalStation,
        int $delayInSeconds,
        bool $wheelchairAccessible,
        int $vehicleType,
        ?string $lastStopId,
        ?string $nextStopId,
        ?string $registrationNumber,
        ?string $company
    ) {
        $this->vehiclePosition = $vehiclePosition;
        $this->routeId = $routeId;
        $this->tripId = $tripId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->finalStation = $finalStation;
        $this->delayInSeconds = $delayInSeconds;
        $this->wheelchairAccessible = $wheelchairAccessible;
        $this->lastStopId = $lastStopId;
        $this->nextStopId = $nextStopId;
        $this->vehicleType = $vehicleType;
        $this->registrationNumber = $registrationNumber;
        $this->company = $company;

        $createdAt = $vehiclePosition->getCreatedAt()->setTime(0, 0, 0);
        $this->dateTripId = $createdAt->getTimestamp() . '_' . $this->tripId;
    }

    public function getVehiclePosition(): IVehiclePosition
    {
        return $this->vehiclePosition;
    }

    public function getRouteId(): string
    {
        return $this->routeId;
    }

    public function getCoordinates(): Coordinates
    {
        return new Coordinates($this->latitude, $this->longitude);
    }

    public function getFinalStation(): string
    {
        return $this->finalStation;
    }

    public function getDelayInSeconds(): int
    {
        return $this->delayInSeconds;
    }

    public function isWheelchairAccessible(): bool
    {
        return $this->wheelchairAccessible;
    }

    public function getLastStopId(): ?string
    {
        return $this->lastStopId;
    }

    public function getNextStopId(): ?string
    {
        return $this->nextStopId;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function getVehicleType(): int
    {
        return $this->vehicleType;
    }

    public function getTripId(): string
    {
        return $this->tripId;
    }

    public function getDateTripId(): string
    {
        return $this->dateTripId;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function hasDelay(): bool
    {
        return $this->getDelayInSeconds() > 0;
    }

    public function getMapLabel(): string
    {
        return sprintf('%s %s', $this->getCompany(), $this->getRegistrationNumber());
    }
}
