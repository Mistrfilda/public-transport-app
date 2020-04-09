<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

use App\Utils\Coordinates;
use JsonSerializable;

class MapObject implements JsonSerializable
{
	/** @var Coordinates */
	private $coordinates;

	/** @var string */
	private $label;

	/** @var string[] */
	private $infoWindowLines;

	/**
	 * @param string[] $infoWindowLines
	 */
	public function __construct(
		Coordinates $coordinates,
		string $label,
		array $infoWindowLines = []
	) {
		$this->coordinates = $coordinates;
		$this->label = $label;
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
			'infoWindowLines' => $this->getInfoWindowLines(),
		];
	}
}
