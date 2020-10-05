<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

use App\Utils\Coordinates;
use JsonSerializable;

class MapObject implements JsonSerializable
{
	private Coordinates $coordinates;

	private string $label;

	private string $mapIcon;

	/** @var string[] */
	private array $infoWindowLines;

	/**
	 * @param string[] $infoWindowLines
	 */
	public function __construct(
		Coordinates $coordinates,
		string $label,
		string $mapIcon,
		array $infoWindowLines = []
	) {
		$this->coordinates = $coordinates;
		$this->label = $label;
		$this->mapIcon = $mapIcon;
		$this->infoWindowLines = $infoWindowLines;
	}

	public function getCoordinates(): Coordinates
	{
		return $this->coordinates;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getMapIcon(): string
	{
		return $this->mapIcon;
	}

	/**
	 * @return string[]
	 */
	public function getInfoWindowLines(): array
	{
		return $this->infoWindowLines;
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize(): array
	{
		return [
			'coordinates' => $this->getCoordinates(),
			'label' => $this->getLabel(),
			'mapIcon' => $this->getMapIcon(),
			'infoWindowLines' => $this->getInfoWindowLines(),
		];
	}
}
